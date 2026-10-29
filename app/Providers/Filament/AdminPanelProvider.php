<?php

namespace App\Providers\Filament;

use App\Filament\Pages\RolesPage;
use App\Filament\Pages\UserPermissionsPage;
use App\Filament\Widgets\AccountWidget;
use App\Filament\Widgets\BranchComplianceOverview;
use App\Filament\Widgets\BranchStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget as FilamentAccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // Auto-discover all Resources under app/Filament/Resources
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )

            ->colors([
                'primary' => Color::Amber,
            ])

            // Explicit pages — Dashboard + our custom permissions page
            ->pages([
                Dashboard::class,
                UserPermissionsPage::class,
                RolesPage::class,
            ])

            // Widgets registered explicitly (order controls dashboard sort)
            ->widgets([
                FilamentAccountWidget::class,
                BranchStatsWidget::class,
                BranchComplianceOverview::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
