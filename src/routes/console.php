<?php

use App\Jobs\GenerateMonthlyServiceCharges;
use App\Jobs\ResetDemoInstance;
use App\Jobs\RetryHestiaSslIssuance;
use App\Jobs\SendAssetExpirationNotifications;
use App\Jobs\SendContractSignatureReminders;
use App\Jobs\SendLicenseExpirationNotifications;
use App\Jobs\SendMaintenanceConfirmationRequests;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new SendLicenseExpirationNotifications())
    ->dailyAt('09:00')
    ->name('license-expiration-notifications')
    ->withoutOverlapping();

Schedule::job(new SendAssetExpirationNotifications())
    ->dailyAt('09:15')
    ->name('asset-expiration-notifications')
    ->withoutOverlapping();

Schedule::job(new SendContractSignatureReminders())
    ->dailyAt('09:30')
    ->name('contract-signature-reminders')
    ->withoutOverlapping();

Schedule::job(new RetryHestiaSslIssuance())
    ->everySixHours()
    ->name('retry-hestia-ssl-issuance')
    ->withoutOverlapping();

Schedule::job(new SendMaintenanceConfirmationRequests())
    ->monthlyOn(1, '07:45')
    ->name('maintenance-confirmation-requests')
    ->withoutOverlapping();

Schedule::job(new GenerateMonthlyServiceCharges())
    ->monthlyOn(1, '08:00')
    ->name('monthly-service-charges')
    ->withoutOverlapping();

// Reset demo instances daily at 03:xx AM
Schedule::job(new ResetDemoInstance('loteos'))
    ->dailyAt('03:00')
    ->name('reset-demo-loteos')
    ->withoutOverlapping();

Schedule::job(new ResetDemoInstance('historias-clinicas'))
    ->dailyAt('03:15')
    ->name('reset-demo-historias-clinicas')
    ->withoutOverlapping();

Schedule::job(new ResetDemoInstance('tallerpro'))
    ->dailyAt('03:30')
    ->name('reset-demo-tallerpro')
    ->withoutOverlapping();