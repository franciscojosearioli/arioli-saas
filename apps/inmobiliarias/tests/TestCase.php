<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use PDO;

abstract class TestCase extends BaseTestCase
{
    protected Tenant $tenant;

    private static ?PDO $centralPdo = null;

    /**
     * Todo lo que un usuario ve (auth, dashboard, perfil) vive en un tenant
     * real (§02/§07 del Artifact) — así que cada test corre dentro de un
     * tenant de verdad, no contra el landlord.
     *
     * No usamos el trait RefreshDatabase de Laravel acá: su heurística para
     * decidir si hace falta remigrar un sqlite :memory: (mirar si el PDO
     * "sigue en transacción" después del test) asume que
     * config('database.default') no cambia durante el test — Stancl lo
     * cambia a propósito (central → tenant → central) en cada request, y
     * esa heurística se confunde ("no such table" o "already an active
     * transaction" en tests que no tienen nada que ver entre sí). En su
     * lugar: se migra la base central una sola vez por proceso y se
     * preserva el mismo PDO :memory: entre tests a mano (mismo truco que
     * usa RefreshDatabase por dentro, sin la parte que choca con el cambio
     * de conexión), y cada test corre en una transacción propia — tanto en
     * la conexión central como en la del tenant.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (static::$centralPdo === null) {
            Artisan::call('migrate', ['--force' => true]);
            static::$centralPdo = DB::connection('sqlite')->getPdo();
        } else {
            DB::connection('sqlite')->setPdo(static::$centralPdo);
        }

        DB::connection('sqlite')->beginTransaction();

        $this->tenant = Tenant::withoutEvents(fn () => Tenant::create(['id' => 'testing']));
        $this->tenant->domains()->create(['domain' => 'testing.inmobiliarias.test']);

        $dbPath = database_path($this->tenant->database()->getName());
        if (! file_exists($dbPath)) {
            touch($dbPath);
        }

        tenancy()->initialize($this->tenant);

        if (! Schema::hasTable('users')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/tenant',
                '--realpath' => false,
            ]);
        }

        DB::connection('tenant')->beginTransaction();

        // El UrlGenerator ya resolvió su root desde APP_URL antes de este
        // punto (el boot de tenancy lo dispara) — un simple
        // config(['app.url' => ...]) no lo actualiza retroactivo.
        // URL::forceRootUrl() sí. Sin esto, toda request de test cae en
        // APP_URL=inmobiliarias.internal (dominio central) y
        // PreventAccessFromCentralDomains la bloquea con 404.
        URL::forceRootUrl('http://testing.inmobiliarias.test');
    }

    protected function tearDown(): void
    {
        if (DB::connection('tenant')->transactionLevel() > 0) {
            DB::connection('tenant')->rollBack();
        }

        tenancy()->end();

        if (DB::connection('sqlite')->transactionLevel() > 0) {
            DB::connection('sqlite')->rollBack();
        }

        parent::tearDown();
    }
}
