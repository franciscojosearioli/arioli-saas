<?php

namespace App\Services\Versioning;

use Illuminate\Support\Facades\DB;

final class VersioningClient implements VersioningClientInterface
{
    public function __construct(
        private readonly string $apiToken,
        private readonly string $adminHost,
        private readonly string $versionUrl,
        private readonly string $updateUrl,
        private readonly string $product,
    ) {}

    public static function fromConfig(): static
    {
        return new static(
            apiToken:   config('saas.api_token', ''),
            adminHost:  config('saas.admin_host'),
            versionUrl: config('saas.version_url'),
            updateUrl:  config('saas.update_url'),
            product:    config('version.product'),
        );
    }

    public function installedVersion(): string
    {
        try {
            return DB::table('app_meta')->where('key', 'version')->value('value')
                ?? config('version.current', '1.0.0');
        } catch (\Throwable) {
            return config('version.current', '1.0.0');
        }
    }

    public function status(): VersionStatus
    {
        $current      = $this->installedVersion();
        $latest       = null;
        $apiReachable = false;

        try {
            $ch = curl_init(
                $this->versionUrl . '?' . http_build_query(['product' => $this->product])
            );
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_TIMEOUT         => 15,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_HTTPHEADER      => [
                    "Host: {$this->adminHost}",
                    "Authorization: Bearer {$this->apiToken}",
                    "Connection: close",
                ],
            ]);
            $raw  = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($code === 200 && $raw) {
                $body         = json_decode($raw, true);
                $latest       = $body['version'] ?? null;
                $apiReachable = true;
            }
        } catch (\Throwable) {}

        return new VersionStatus(
            currentVersion:  $current,
            latestVersion:   $latest,
            updateAvailable: $latest !== null && version_compare($latest, $current, '>'),
            apiReachable:    $apiReachable,
        );
    }

    public function requestUpdate(string $tenantId): bool
    {
        try {
            $payload = json_encode(['tenant_id' => $tenantId, 'product' => $this->product]);
            $ch      = curl_init($this->updateUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_TIMEOUT         => 15,
                CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => $payload,
                CURLOPT_HTTPHEADER      => [
                    "Host: {$this->adminHost}",
                    "Authorization: Bearer {$this->apiToken}",
                    "Content-Type: application/json",
                    "Connection: close",
                ],
            ]);
            $code = curl_getinfo(curl_exec($ch) ? $ch : $ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $code >= 200 && $code < 300;
        } catch (\Throwable) {
            return false;
        }
    }
}
