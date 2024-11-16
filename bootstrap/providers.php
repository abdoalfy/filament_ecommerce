<?php

use Laravel\Socialite\SocialiteServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    \Devaslanphp\AutoTranslate\AutoTranslateProvider::class,
    SocialiteServiceProvider::class,

];
