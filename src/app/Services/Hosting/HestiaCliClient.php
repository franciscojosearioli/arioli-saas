<?php

namespace App\Services\Hosting;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * Ejecuta los comandos v-* de HestiaCP por SSH contra host.arioli.dev.
 * Expone métodos de alto nivel por dominio (no comandos v-* sueltos) para
 * que cambiar de panel el día de mañana sea reemplazar esta clase, no
 * reescribir HestiaCpProvider ni nada que lo use.
 */
class HestiaCliClient
{
    public function createHostingAccount(string $username, string $password, string $email, string $domain, string $clientName, ?string $package = null): array
    {
        [$firstName, $lastName] = $this->splitName($clientName);

        // Orden real de v-add-user: USER PASSWORD EMAIL [PACKAGE] [FNAME] [LNAME]
        // (confirmado corrigiendo un error real: "package Cliente doesn't exist"
        // al pasar el nombre en la posición del package).
        $addUser = $this->run('v-add-user', [
            $username,
            $password,
            $email,
            $package ?? 'default',
            $firstName,
            $lastName,
        ]);

        if (! $addUser['success']) {
            return $addUser;
        }

        $addDomain = $this->run('v-add-web-domain', [$username, $domain]);

        if (! $addDomain['success']) {
            return $addDomain;
        }

        // v-add-mail-domain y v-add-web-domain-alias todavía no están en la
        // whitelist de 8 comandos de la clave de automatización remota (ver
        // hestia-automation-wrapper.sh en host.arioli.dev) — hasta que se
        // agreguen ahí, esto falla con "Comando no permitido" y solo queda
        // logueado, sin cortar el alta de la cuenta. El día que se habiliten,
        // empieza a funcionar solo, sin tocar este código de nuevo.
        $mailDomain = $this->addMailDomain($username, $domain);

        if (! $mailDomain['success']) {
            Log::warning('HestiaCliClient: no se pudo crear el dominio de correo automáticamente (revisar whitelist del wrapper)', [
                'username' => $username,
                'domain'   => $domain,
                'output'   => $mailDomain['output'],
            ]);
        }

        $webmailAlias = $this->addWebDomainAlias($username, $domain, "webmail.{$domain}");

        if (! $webmailAlias['success']) {
            Log::warning('HestiaCliClient: no se pudo agregar el alias webmail. automáticamente (revisar whitelist del wrapper)', [
                'username' => $username,
                'domain'   => $domain,
                'output'   => $webmailAlias['output'],
            ]);
        }

        // Let's Encrypt puede fallar si el DNS todavía no apunta al servidor —
        // no es fatal para la creación de la cuenta en sí, solo se loguea.
        // Queda pendiente de reintentar a mano con issueSsl() una vez que el
        // DNS del cliente ya apunte acá (ver Admin\HostingController). Si el
        // alias de webmail se pudo agregar, el mismo certificado lo cubre.
        $ssl = $this->issueSsl($username, $domain);

        if (! $ssl['success']) {
            Log::warning('HestiaCliClient: no se pudo emitir SSL automáticamente (DNS puede no apuntar todavía)', [
                'username' => $username,
                'domain'   => $domain,
                'output'   => $ssl['output'],
            ]);
        }

        return $addDomain;
    }

    /**
     * Emite (o reintenta) el certificado Let's Encrypt del dominio — separado
     * de createHostingAccount() para poder reintentarlo después de que el
     * DNS del cliente ya apunte acá, sin tener que recrear toda la cuenta.
     */
    public function issueSsl(string $username, string $domain): array
    {
        return $this->run('v-add-letsencrypt-domain', [$username, $domain]);
    }

    /**
     * Crea el contenedor de correo del dominio (no las casillas individuales
     * — esas requieren elegir direcciones puntuales, se cargan a mano desde
     * el panel o vía v-add-mail-account el día que también se whiteliste).
     * Requiere que el wrapper remoto permita v-add-mail-domain.
     */
    public function addMailDomain(string $username, string $domain): array
    {
        return $this->run('v-add-mail-domain', [$username, $domain]);
    }

    /**
     * Agrega un alias al dominio web (ej. webmail.dominio.com) para que
     * quede cubierto por el mismo certificado SSL que la Roundcube de
     * HestiaCP necesita para andar por HTTPS. Requiere que el wrapper
     * remoto permita v-add-web-domain-alias.
     */
    public function addWebDomainAlias(string $username, string $domain, string $alias): array
    {
        return $this->run('v-add-web-domain-alias', [$username, $domain, $alias]);
    }

    public function changePassword(string $username, string $newPassword): array
    {
        return $this->run('v-change-user-password', [$username, $newPassword]);
    }

    public function suspend(string $username): array
    {
        return $this->run('v-suspend-user', [$username]);
    }

    public function unsuspend(string $username): array
    {
        return $this->run('v-unsuspend-user', [$username]);
    }

    public function deleteAccount(string $username): array
    {
        return $this->run('v-delete-user', [$username]);
    }

    public function listUser(string $username): array
    {
        return $this->run('v-list-user', [$username, 'json']);
    }

    /**
     * Backup real de HestiaCP: archivos + todas las bases de datos de la
     * cuenta, comprimidos en /home/<username>/backup/. Puede tardar varios
     * minutos en un sitio real — timeout largo, no los 30s de default.
     * Requiere que el wrapper remoto permita v-backup-user.
     */
    public function backupUser(string $username, int $timeoutSeconds = 1200): array
    {
        return $this->run('v-backup-user', [$username], $timeoutSeconds);
    }

    /**
     * Trae el backup MÁS RECIENTE generado por backupUser() al storage local
     * de Arioli — separado de run() porque acá el output es contenido
     * binario real (un .tar), no texto para loguear/mostrar, así que no se
     * le puede aplicar trim() ni tratarlo como el resto de los comandos.
     * No recibe nombre de archivo a propósito: HestiaCP nombra el backup con
     * su propio timestamp interno, así que el wrapper remoto es quien
     * resuelve "el más nuevo de /home/<username>/backup/" y lo manda por
     * stdout — evita que este lado tenga que adivinar el formato exacto del
     * nombre. Requiere un comando adicional en el wrapper (no es un v-* real
     * de HestiaCP) — ver nota en el plan de mantenimiento automático.
     */
    public function fetchLatestBackupFile(string $username, string $localPath, int $timeoutSeconds = 900): array
    {
        if (preg_match('/[\s;&|`$()<>"\']/', $username) || str_contains($username, '..')) {
            return ['success' => false, 'output' => "Usuario inválido: {$username}"];
        }

        $config = config('hosting_panel.hestiacp');
        $remoteCommand = "sudo /usr/local/hestia/bin/v-fetch-backup {$username}";

        $sshCommand = [
            'ssh',
            '-i', $config['private_key'],
            '-o', 'BatchMode=yes',
            '-o', 'ConnectTimeout=15',
            '-o', 'StrictHostKeyChecking=accept-new',
            '-o', 'UserKnownHostsFile=' . storage_path('app/ssh/known_hosts'),
            '-p', (string) $config['port'],
            "{$config['user']}@{$config['host']}",
            $remoteCommand,
        ];

        try {
            $result = Process::timeout($timeoutSeconds)->run($sshCommand);

            if (! $result->successful()) {
                Log::error('HestiaCliClient: fetchLatestBackupFile falló', [
                    'username' => $username,
                    'output'   => trim($result->errorOutput()),
                ]);

                return ['success' => false, 'output' => trim($result->errorOutput()) ?: 'Comando no permitido o archivo inexistente.'];
            }

            file_put_contents($localPath, $result->output());

            return ['success' => true, 'output' => 'OK', 'bytes' => filesize($localPath)];
        } catch (\Throwable $e) {
            return ['success' => false, 'output' => 'Error al transferir el archivo: ' . $e->getMessage()];
        }
    }

    /**
     * El nombre real del cliente ("María José Pérez") no puede viajar tal cual
     * como argumento — el transporte no soporta espacios (ver run(), abajo).
     * Se sanea a solo letras y se colapsa a una palabra por campo, sin perder
     * el nombre real del cliente como sí pasaba antes (quedaba hardcodeado
     * "Cliente Arioli" para todos).
     *
     * @return array{0: string, 1: string} [firstName, lastName]
     */
    private function splitName(string $name): array
    {
        $clean = preg_replace('/[^\p{L}\s]/u', '', $name) ?? '';
        $parts = preg_split('/\s+/', trim($clean), -1, PREG_SPLIT_NO_EMPTY);

        $first = $parts[0] ?? 'Cliente';
        $last  = implode('', array_slice($parts, 1)) ?: 'Arioli';

        return [$first, $last];
    }

    /**
     * El wrapper remoto (hestia-automation-wrapper.sh) ejecuta el comando
     * validado vía `exec $cmd` — sin un shell intermedio, a propósito, para
     * que `;`/`|`/`&&` nunca se interpreten como separadores de comando.
     * Eso significa que acá NO hay que citar los argumentos con
     * escapeshellarg() (esas comillas quedarían como texto literal al no
     * haber shell que las interprete del otro lado) — en cambio, se valida
     * que ningún argumento tenga espacios ni caracteres de shell, ya que acá
     * el único "parseo" del otro lado es un simple split por espacios.
     */
    private function run(string $command, array $args, int $timeoutSeconds = 30): array
    {
        $config = config('hosting_panel.hestiacp');

        foreach ($args as $arg) {
            if (preg_match('/[\s;&|`$()<>"\']/', (string) $arg)) {
                return [
                    'success'   => false,
                    'exit_code' => 1,
                    'output'    => "Argumento inválido para {$command}: contiene espacios o caracteres no permitidos.",
                ];
            }
        }

        $remoteCommand = "sudo /usr/local/hestia/bin/{$command} " . implode(' ', $args);

        $sshCommand = [
            'ssh',
            '-i', $config['private_key'],
            '-o', 'BatchMode=yes',
            '-o', 'ConnectTimeout=15',
            '-o', 'StrictHostKeyChecking=accept-new',
            '-o', 'UserKnownHostsFile=' . storage_path('app/ssh/known_hosts'),
            '-p', (string) $config['port'],
            "{$config['user']}@{$config['host']}",
            $remoteCommand,
        ];

        // Process::run() con timeout tira ProcessTimedOutException si el comando
        // se cuelga (ej. v-add-letsencrypt-domain esperando una validación HTTP
        // que nunca va a llegar porque el DNS todavía no apunta acá) — sin este
        // try/catch esa excepción se propaga sin control y hace que el Job
        // entero reintente desde cero, chocando con "el usuario ya existe" en
        // el reintento (bug real, encontrado migrando un cliente real).
        try {
            $result = Process::timeout($timeoutSeconds)->run($sshCommand);

            $success = $result->successful();
            $exitCode = $result->exitCode();
            $output = trim($result->output() ?: $result->errorOutput());
        } catch (\Throwable $e) {
            $success = false;
            $exitCode = -1;
            $output = 'Comando agotó el tiempo de espera o falló la conexión SSH: ' . $e->getMessage();
        }

        if (! $success) {
            Log::error('HestiaCliClient: comando falló', [
                'command'    => $command,
                'exit_code'  => $exitCode,
                'output'     => $output,
            ]);
        }

        return [
            'success'   => $success,
            'exit_code' => $exitCode,
            'output'    => $output,
        ];
    }
}
