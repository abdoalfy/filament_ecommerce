<?php

namespace App\Providers\Filament;

use Filament\Support\Colors\Color;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Awcodes\Overlook\{
    Widgets\OverlookWidget,
    OverlookPlugin
};
use Filament\Http\Middleware\{
    Authenticate,
    DisableBladeIconComponents,
    DispatchServingFilamentEvent
};
use Filament\{
    Pages,
    Panel,
    PanelProvider,
    Widgets
};
use Illuminate\Cookie\Middleware\{
    AddQueuedCookiesToResponse,
    EncryptCookies
};
use Illuminate\Session\Middleware\{
    AuthenticateSession,
    StartSession
};

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName(__("E-commerce")) // SET THE NAME OF THE WEBSITE
            // ->brandLogo(url()) // SET THE LOGO URL
            // ->favicon(url()) // SET THE FAVICON URL
            // ->colors([
            //     'primary' => Color::Amber,
            // ])
            ->colors([
                // 'primary' => Color::Amber,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Blue,
                'primary' => Color::Emerald,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //Pages\Dashboard::class,
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
            ->authGuard('admin')
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                \Filament\SpatieLaravelTranslatablePlugin::make()
                        ->defaultLocales(['en', 'ar']),
                // \Filament\SpatieLaravelTranslatablePlugin::make()->defaultLocales([config('app.locale')]),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \TomatoPHP\FilamentMenus\FilamentMenusPlugin::make(),
                \TomatoPHP\FilamentApi\FilamentAPIPlugin::make(),
                \LaraZeus\Sky\SkyPlugin::make(),
                OverlookPlugin::make()
                ->sort(2)
                ->columns([
                    'default' => 1,
                    'sm' => 2,
                    'md' => 2,
                    'lg' => 3,
                    'xl' => 4,
                    '2xl' => null,
                ]),

            ])->resources([
                config('filament-logger.activity_resource')
            ])->viteTheme('resources/css/filament/admin/theme.css');
    }
}
