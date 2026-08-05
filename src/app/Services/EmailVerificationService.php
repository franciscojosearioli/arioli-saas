<?php

namespace App\Services;

/**
 * Verificación de emails en tiempo real: formato + dominio con MX válido +
 * (best-effort) chequeo SMTP de existencia del buzón vía RCPT TO.
 *
 * El chequeo SMTP es solo informativo salvo cuando el servidor destino
 * RECHAZA explícitamente el buzón (550/551/553) — muchos proveedores
 * grandes (Gmail, Outlook) no permiten verificar buzones así por
 * anti-spam, así que un resultado inconcluso NO se trata como inválido.
 */
class EmailVerificationService
{
    public function verify(string $email): array
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'status' => 'invalid_format', 'message' => 'El formato del email no es válido.'];
        }

        $domain = substr((string) strrchr($email, '@'), 1);

        if ($domain === '' || ! $this->hasMailServer($domain)) {
            return ['valid' => false, 'status' => 'no_mail_server', 'message' => 'El dominio de este email no puede recibir correos.'];
        }

        $smtpResult = $this->probeSmtp($email, $domain);

        if ($smtpResult === false) {
            return ['valid' => false, 'status' => 'rejected', 'message' => 'Este email no existe en el servidor destino.'];
        }

        if ($smtpResult === true) {
            return ['valid' => true, 'status' => 'confirmed', 'message' => 'Email verificado correctamente.'];
        }

        return ['valid' => true, 'status' => 'unconfirmed', 'message' => 'Formato y dominio válidos.'];
    }

    private function hasMailServer(string $domain): bool
    {
        return checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A');
    }

    /**
     * @return bool|null true=el servidor acepta el buzón, false=lo rechazó explícitamente, null=inconcluso
     */
    private function probeSmtp(string $email, string $domain): ?bool
    {
        $hosts = $this->mxHosts($domain);
        $host  = $hosts[0] ?? null;

        if (! $host) {
            return null;
        }

        $fromDomain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'arioli.dev';

        $conn = @fsockopen($host, 25, $errno, $errstr, 3);
        if (! $conn) {
            return null;
        }

        stream_set_timeout($conn, 3);

        try {
            $this->readResponse($conn); // banner
            $this->sendCommand($conn, "EHLO {$fromDomain}");
            $this->sendCommand($conn, "MAIL FROM:<verificacion@{$fromDomain}>");
            $response = $this->sendCommand($conn, "RCPT TO:<{$email}>");
            fwrite($conn, "QUIT\r\n");
            fclose($conn);

            $code = (int) substr(trim($response), 0, 3);

            if (in_array($code, [250, 251], true)) {
                return true;
            }
            if (in_array($code, [550, 551, 553], true)) {
                return false;
            }

            return null;
        } catch (\Throwable) {
            @fclose($conn);

            return null;
        }
    }

    private function mxHosts(string $domain): array
    {
        if (getmxrr($domain, $hosts, $weights)) {
            array_multisort($weights, $hosts);

            return $hosts;
        }

        return checkdnsrr($domain, 'A') ? [$domain] : [];
    }

    private function sendCommand($conn, string $command): string
    {
        fwrite($conn, $command."\r\n");

        return $this->readResponse($conn);
    }

    private function readResponse($conn): string
    {
        $response = '';

        while ($line = fgets($conn, 512)) {
            $response = $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        return $response;
    }
}
