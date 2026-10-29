<?php

namespace App\Providers;

use App\Auth\PlainTextUserProvider;
use App\Models\BranchComplianceRecord;
use App\Observers\BranchComplianceRecordObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Auto-compute status and renewal_due on every save
        BranchComplianceRecord::observe(BranchComplianceRecordObserver::class);

        // Register a custom auth provider that compares passwords as plain text
        Auth::provider('plaintext', function ($app, array $config) {
            return new PlainTextUserProvider(
                $app['hash'],
                $config['model']
            );
        });
    }
}
