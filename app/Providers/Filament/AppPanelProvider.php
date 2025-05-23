<?php

namespace App\Providers\Filament;

use Carbon\Carbon;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Enums\ThemeMode;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Awcodes\FilamentStickyHeader\StickyHeaderPlugin;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use EightCedars\FilamentInactivityGuard\FilamentInactivityGuardPlugin;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->defaultThemeMode(ThemeMode::Light)
            ->brandLogoHeight('65px')
            ->brandLogo(asset('images/logo/smis-logo.png'))
            ->favicon(asset('images/logo/smis-icon.ico'))
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('17rem')
            ->spa()
            ->brandName('SMIS')
            ->id('app')
            ->path('app')
            ->login()
            ->registration()
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
                'rose' => Color::Pink,
                'indigo' => Color::Indigo,
                'emerald' => Color::Emerald,
                'cyan' => '#30D5C8',
                'orange' => Color::Orange,
                'yellow' => Color::Amber,
                'green' => '#009900',
                'red' => Color::Red,
                'blue' => Color::Blue,
                'pink' => Color::Pink,
                'teal' => Color::Teal,
                'violet' => '#7F00FF',
                'fuchsia' => Color::Fuchsia,
                'slate' => Color::Slate,
                'zinc' => Color::Zinc,
                'stone' => Color::Stone,
                'amber' => Color::Amber,
                'lime' => Color::Lime,
                'rose' => Color::Rose,
                'neutral' => Color::Neutral,
                'stone' => Color::Stone,
                'muted' => '#bbbbbb',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
            ])
            ->plugins([
                FilamentInactivityGuardPlugin::make()
                    ->inactiveAfter(5 * Carbon::SECONDS_PER_MINUTE)
                    ->showNoticeFor(1 * Carbon::SECONDS_PER_MINUTE)
                    ->enabled(!app()->isLocal())
                    ->keepActiveOn(['change', 'select', 'mousemove'], mergeWithDefaults: true),

                // StickyHeaderPlugin::make()
                //     ->floating(),


            ]);
    }
}
