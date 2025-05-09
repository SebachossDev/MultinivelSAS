<?php

namespace App\Providers\Filament;

use App\Filament\Pages\CustomDashboard;
use App\Filament\Widgets\InventoryStats;
use App\Filament\Widgets\LeastSoldProductsChart;
use App\Filament\Widgets\MonthlySalesChart;
use App\Filament\Widgets\MonthlyUserRegistrationsChart;
use App\Filament\Widgets\MostSoldProductsChart;
use App\Filament\Widgets\UserDistributionChart;
use App\Filament\Widgets\UserStatsWidget;
use App\Livewire\CustomRegister;
use App\Livewire\DepartmentCityProfileComponent;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin;
use Filament\Navigation\MenuItem;
use Filament\Pages\Dashboard;
use Joaopaulolndev\FilamentEditProfile\Pages\EditProfilePage;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->topNavigation()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->registration(CustomRegister::class)
            ->passwordReset()
            ->colors([
                'primary' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                CustomDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                UserStatsWidget::class,
                MonthlyUserRegistrationsChart::class,         
                InventoryStats::class,
                MonthlySalesChart::class,
                MostSoldProductsChart::class,
                UserDistributionChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                \App\Http\Middleware\CheckUserActive::class,
            ])

            ->plugins([
                FilamentEditProfilePlugin::make()
                    ->slug('my-profile')
                    ->setTitle('Mi Perfil')
                    ->setNavigationLabel('Mi Perfil')
                    ->setIcon('heroicon-o-user')
                    ->shouldRegisterNavigation(false)
                    ->customProfileComponents([
                        DepartmentCityProfileComponent::class,
                    ])
                    ->shouldShowAvatarForm(
                        value: true,
                        directory: 'avatars',
                        rules: 'mimes:jpeg,png|max:2048' 
                    )
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(fn() => \Illuminate\Support\Facades\Auth::user()->name)
                    ->url(fn (): string => EditProfilePage::getUrl())
                    ->icon('heroicon-m-user-circle')
            ])
            ;
    }
}
