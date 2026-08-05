<?php

namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;

/**
 * Agregados de plata de Charge/ChargePayment para la sección "Finanzas" del
 * dashboard (cards grandes, ver admin.dashboard) — ya no es un
 * DashboardProviderInterface registrado en config/dashboard_providers.php:
 * estas cifras se promovieron a cards propias en vez de competir por espacio
 * en el grid genérico de widgets chicos (reorganización 2026-07-28).
 */
class ChargeDashboardProvider
{
    /**
     * Saldo pendiente real (monto - pagos registrados) de todos los clientes,
     * agrupado por moneda. Excluye cobros bundleados (bundled_into_charge_id
     * no nulo — ya representados por el Charge combinado) y los
     * cancelados/rechazados, mismo criterio que ClientPendingChargesSummaryService.
     */
    public function pendingBalanceByCurrency(): array
    {
        $totals = DB::table('charges')
            ->whereNull('bundled_into_charge_id')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->groupBy('currency')
            ->selectRaw('currency, SUM(amount) as total')
            ->pluck('total', 'currency');

        $paid = $this->paidTotalByCurrency();

        $balances = [];
        foreach ($totals as $currency => $total) {
            $balances[$currency] = round((float) $total - ($paid[$currency] ?? 0), 2);
        }

        return $balances;
    }

    /**
     * Todo lo efectivamente cobrado históricamente (ChargePayment), agrupado
     * por moneda — mismo criterio de exclusión que arriba.
     */
    public function paidTotalByCurrency(): array
    {
        return DB::table('charge_payments')
            ->join('charges', 'charges.id', '=', 'charge_payments.charge_id')
            ->whereNull('charges.bundled_into_charge_id')
            ->whereNotIn('charges.status', ['cancelled', 'rejected'])
            ->groupBy('charges.currency')
            ->selectRaw('charges.currency, SUM(charge_payments.amount) as total')
            ->pluck('total', 'currency')
            ->map(fn ($total) => round((float) $total, 2))
            ->toArray();
    }

    /**
     * Lo cobrado (ChargePayment) dentro del mes calendario actual — para
     * distinguir "cobrado a clientes este mes" de "ingresos por licencias
     * SaaS este mes" (Order), que son dos fuentes de plata distintas.
     */
    public function paidThisMonthByCurrency(): array
    {
        return DB::table('charge_payments')
            ->join('charges', 'charges.id', '=', 'charge_payments.charge_id')
            ->whereNull('charges.bundled_into_charge_id')
            ->whereNotIn('charges.status', ['cancelled', 'rejected'])
            ->where('charge_payments.paid_at', '>=', now()->startOfMonth())
            ->groupBy('charges.currency')
            ->selectRaw('charges.currency, SUM(charge_payments.amount) as total')
            ->pluck('total', 'currency')
            ->map(fn ($total) => round((float) $total, 2))
            ->toArray();
    }

    public function formatByCurrency(array $amountsByCurrency): string
    {
        if (empty($amountsByCurrency)) {
            return '$0';
        }

        return collect($amountsByCurrency)
            ->map(fn ($amount, $currency) => "{$currency} ".number_format($amount, 2, ',', '.'))
            ->implode(' · ');
    }
}
