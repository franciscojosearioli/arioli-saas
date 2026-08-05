<?php

namespace App\Services\License;

use Illuminate\Support\Facades\Cache;

final class LicenseClient implements LicenseClientInterface
{
    private const CACHE_TTL = 300; // 5 minutos, igual que ValidateLicense

    public function __construct(
        private readonly string $apiToken,
        private readonly string $adminHost,
        private readonly string $infoUrl,
        private readonly string $product,
    ) {}

    public static function fromConfig(): static
    {
        // config('saas.api_token', '') NO cae al default cuando la key
        // existe con valor null (SAAS_API_TOKEN sin definir en .env) — el
        // default de config() solo aplica si la key es inexistente, no si
        // es null. Nunca se notó hasta que algo llamó a fromConfig() de
        // verdad en un test (la vista de login, recién con el hint de
        // demo) — el constructor typed `string $apiToken` rechazaba null.
        return new self(
            apiToken: config('saas.api_token') ?? '',
            adminHost: config('saas.admin_host'),
            infoUrl: config('saas.info_url'),
            product: config('version.product'),
        );
    }

    public function getInfo(): LicenseInfo
    {
        $host = request()->getHost();
        $key = "license_info_{$host}";

        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $this->buildInfo($cached);
        }

        $data = $this->fetchData($host);
        if ($data !== null) {
            Cache::put($key, $data, self::CACHE_TTL);

            return $this->buildInfo($data);
        }

        return $this->failOpen($host);
    }

    private function buildInfo(array $d): LicenseInfo
    {
        return new LicenseInfo(
            product: $d['product'],
            plan: $d['plan'],
            licenseType: $d['license_type'],
            active: $d['active'],
            startsAt: $d['starts_at'],
            expiresAt: $d['expires_at'],
            daysRemaining: $d['days_remaining'],
            domain: $d['domain'],
            installedVersion: $d['installed_version'],
            latestVersion: $d['latest_version'],
            lastValidatedAt: $d['last_validated_at'],
        );
    }

    public function isDemo(): bool
    {
        return $this->getInfo()->isDemo();
    }

    public function isActive(): bool
    {
        return $this->getInfo()->active;
    }

    public function getType(): string
    {
        return $this->getInfo()->licenseType;
    }

    private function fetchData(string $host): ?array
    {
        try {
            $tenantId = explode('.', $host)[0] ?? '';
            $url = $this->infoUrl.'?'.http_build_query([
                'tenant' => $tenantId,
                'product' => $this->product,
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER => [
                    "Host: {$this->adminHost}",
                    "Authorization: Bearer {$this->apiToken}",
                    "X-Tenant-Host: {$host}",
                    'Connection: close',
                ],
            ]);
            $raw = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $raw) {
                $data = json_decode($raw, true);

                return [
                    'product' => $data['product'] ?? $this->product,
                    'plan' => $data['plan'] ?? '',
                    'license_type' => $data['license_type'] ?? 'commercial',
                    'active' => $data['active'] ?? true,
                    'starts_at' => $data['starts_at'] ?? null,
                    'expires_at' => $data['expires_at'] ?? null,
                    'days_remaining' => $data['days_remaining'] ?? null,
                    'domain' => $data['domain'] ?? $host,
                    'installed_version' => $data['installed_version'] ?? '',
                    'latest_version' => $data['latest_version'] ?? '',
                    'last_validated_at' => $data['last_validated_at'] ?? '',
                ];
            }
        } catch (\Throwable) {
        }

        return null;
    }

    private function failOpen(string $host): LicenseInfo
    {
        return new LicenseInfo(
            product: $this->product, plan: '', licenseType: 'commercial',
            active: true, startsAt: null, expiresAt: null,
            daysRemaining: null, domain: $host,
            installedVersion: '', latestVersion: '', lastValidatedAt: '',
        );
    }
}
