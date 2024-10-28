<?php

namespace App\Providers;

use App\Policies\ActivityPolicy;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;

use Illuminate\Support\{
    Facades\Gate,
    Facades\Route,
    ServiceProvider
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // add all the needed policeis here
        Gate::policy(Activity::class, ActivityPolicy::class,);
        
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar','en']); // also accepts a closure
        });

        // this will fix livewire issue with xampp
        Livewire::setUpdateRoute(function ($handle) {

            return Route::post(env('LIVEWIRE_UPDATE_PATH'), $handle)->name('custom-livewire.update');
            
            });
            
            Livewire::setScriptRoute(function ($handle) {
            
            return Route::get(env('LIVEWIRE_JAVASCRIPT_PATH'), $handle);
            
            });
    }
}
