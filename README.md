
  

# New Start Project

  

## Requirements

  

> PHP version 8.2+

> Laravel version 11+

## Installation
- Start by cloning the git
```
git clone https://github.com/serv5group/laravel-filament.git

composer install
```

- After the following command change DB name in `.env` and create it in `Phpmyadmin`
```
copy .env.example .env 

php artisan key:generate
```
```  
// You have to create the db first before running this command.
php artisan migrate 

// if you have a problem run this command instead
php artisan migrate:fresh
```
  
- You can edit the admin info in this file `AdminUserSeeder`
```
php artisan db:seed --class=AdminUserSeeder 'AdminUserSeeder'
```
- After running the previous command **`You  MUST`** go to `User` and `Admin` models and uncomment line 80 to 96 in saving function after that run the following command
```
php artisan shield:super-admin --user=1
php artisan make:filament-user // Don't run this command if you run the previous one
npm install
npm run build
```


- To make the project work correctly on XAMPP you need to fix post login error first by running the following commands after installing filament in command line

```

composer require livewire/livewire

php artisan vendor:publish --force --tag=livewire:assets

php artisan livewire:publish --config

```

- After that add this line to `config/livewire.php`. Make sure to change `new-start` to the name of the project folder

```

'asset_url' => '/new-start/public/vendor/livewire/livewire.js',

```

  

- Third step is to add this line in `AppServiceProvider.php` in `boot()`

  

```

use Livewire\Livewire;

  

Livewire::setUpdateRoute(function ($handle) {

return Route::post(env('LIVEWIRE_UPDATE_PATH'), $handle)->name('custom-livewire.update');

});

Livewire::setScriptRoute(function ($handle) {

return Route::get(env('LIVEWIRE_JAVASCRIPT_PATH'), $handle);

});

```

  

Last step is to add the following in `.env`. Make sure to change `new-start` to the name of the project folder

  

```

LIVEWIRE_UPDATE_PATH=new-start/livewire/update

LIVEWIRE_JAVASCRIPT_PATH=new-start/public/livewire/livewire.js

```

- After that don't forget to run `npm install` and `npm run dev` for breeze in cli or you can copy all public/build/assets to /build/assets without running `npm run dev`
- Run the following command to generate the permissions and policies

```
php artisan shield:generate --all
```
- Log into the admin panel then go to roles page and click on edit for `super_admin` role and click on save in the edit page to save the permissions for the role and the admin user

## Filamentphp

  

https://filamentphp.com/docs/3.x/panels/installation

  
  

## bezhansalleh/filament-language-switch

  

https://github.com/bezhanSalleh/filament-language-switch

  

- First you have to make sure that php intl package is active in php.ini

  

- To add locale first make sure to run if the `lang` folder doesn't exist.

```

php artisan lang:publish

```

To add or remove locale, you can modify this code in `AppServiceProvider.php`

```

LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {

$switch->locales(['ar','en']); // also accepts a closure

});

```

==============================================================

  

## Filemant blog

  

https://github.com/thefireflytech/filament-blog

  

Keep in mind that if you want to translate a resource in this package, you have to edit the resource in the vendor folder to include the translation for this package like the following code for `CategoryResource.php`

```

public static function getTranslatableLocales(): array

{

return ['ar', 'en'];

}

  

public static function getNavigationLabel(): string

{

return __('strings.categories');

}

  

public static function getModelLabel(): string

{

return __('strings.category');

}

public static function getPluralModelLabel(): string

{

return __('strings.categories');

}

```

  

## Spatie laravel translatable plugin

  

https://github.com/filamentphp/spatie-laravel-translatable-plugin

Making a model translatable

The required steps to make a model translatable are:

First, you need to add the Spatie\Translatable\HasTranslations-trait.
Next, you should create a public property $translatable which holds an array with all the names of attributes you wish to make translatable.
Finally, you should make sure that all translatable attributes are set to the json-datatype in your database. If your database doesn't support json-columns, use text.

Here's an example of a prepared model:
```
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NewsItem extends Model
{
    use HasTranslations;

    public $translatable = ['name'];
}
```  

. To prepare your resource class, You have to add `use Translatable;` in your resource file like:

```

use Filament\Resources\Concerns\Translatable;

use Filament\Resources\Resource;

  

class BlogPostResource extends Resource

{

use Translatable;

}

```

- To Make resource pages translatable, after preparing your resource class, you must make each of your resource's pages translatable too. You can find your resource's pages in the Pages directory of each resource folder. To prepare a page, you must apply the corresponding Translatable trait to it, and install a` LocaleSwitcher` header action:

  

```

use Filament\Actions;

use Filament\Resources\Pages\ListRecords;

  

class ListBlogPosts extends ListRecords

{

use ListRecords\Concerns\Translatable;

protected function getHeaderActions(): array

{

return [

Actions\LocaleSwitcher::make(),

];

}

}

```

  

```

use Filament\Actions;

use Filament\Resources\Pages\CreateRecord;

  

class CreateBlogPost extends CreateRecord

{

use CreateRecord\Concerns\Translatable;

protected function getHeaderActions(): array

{

return [

Actions\LocaleSwitcher::make(),

// ...

];

}

// ...

}

```

  

```

use Filament\Actions;

use Filament\Resources\Pages\EditRecord;

  

class EditBlogPost extends EditRecord

{

use EditRecord\Concerns\Translatable;

protected function getHeaderActions(): array

{

return [

Actions\LocaleSwitcher::make(),

// ...

];

}

// ...

}

```

- And if you have a` ViewRecord` page for your resource:

```

use Filament\Actions;

use Filament\Resources\Pages\ViewRecord;

  

class ViewBlogPost extends ViewRecord

{

use ViewRecord\Concerns\Translatable;

protected function getHeaderActions(): array

{

return [

Actions\LocaleSwitcher::make(),

// ...

];

}

// ...

}

```

  

- If you're using a simple resource, you can make the `ManageRecords` page translatable instead:

```

use Filament\Actions;

use Filament\Resources\Pages\ManageRecords;

  

class ManageBlogPosts extends ListRecords

{

use ManageRecords\Concerns\Translatable;

protected function getHeaderActions(): array

{

return [

Actions\LocaleSwitcher::make(),

// ...

];

}

// ...

}

```

  

## z3d0x/filament-logger

  

https://github.com/Z3d0X/filament-logger

  

- To log any resource you want, you have to add the following `trait` to the model itself /not the resource/

```

use Spatie\Activitylog\{

Traits\LogsActivity,

LogOptions

};

  
  

use LogsActivity;

```

  

- and add the following method also to the model

```

/**

* This function will make sure to log all fields in this model

* @return \Spatie\Activitylog\LogOptions

*/

public function getActivitylogOptions(): LogOptions

{

return LogOptions::defaults()

->logAll();

}

```

  

## serv5group/whatscloudapi

  

You have to provide `needbot's`  `(whatsapp_token` and `whatsapp_instnace_id`) for this package when sending message to WhatsApp. You can save them either in`.env` or in DB

  

## Filament shield

  

https://github.com/bezhanSalleh/filament-shield

  

Remember to generate a policy after creating a page or resource use the following command to generate ALL THE POLICIES and set the permissions into the DB

```

php artisan shield:generate --all

```

Or use the following command to generate policies for the newly created pages of resources only

```

php artisan shield:generate --all --ignore-existing-policies

```

## Filament Google Analytics (GA4)

https://github.com/bezhansalleh/filament-google-analytics

follow the directions on [Spatie's Laravel Google Analytics package](https://github.com/spatie/laravel-analytics) for getting your credentials, then put them here:

  

```

yourapp/storage/app/analytics/service-account-credentials.json

```

Also add this to the `.env` for your Filament PHP app:

```

ANALYTICS_PROPERTY_ID=

```

  

# Usage

(https://github.com/bezhansalleh/filament-google-analytics#usage)

  

All the widgets are enabled by default in a dedicated `Google Analytics Dashboard`. You can enable or disable a specific widget or the dedicated dashboard all together or show and hide some from the main `Filament Dashboard` from the config `filament-google-analytics`.

  

Publish the config files and set your settings:

```

php artisan vendor:publish --tag=filament-google-analytics-config

```

  

#### Available Widgets

(https://github.com/bezhansalleh/filament-google-analytics#available-widgets)

  

```

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\PageViewsWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\VisitorsWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersSevenDayWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersTwentyEightDayWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsDurationWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByCountryWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsByDeviceWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget::class,

\BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget::class,

```

  

#### Custom Dashboard

(https://github.com/bezhansalleh/filament-google-analytics#custom-dashboard)

  

Though this plugin comes with a default dashboard, but sometimes you might want to change `navigationLabel` or `navigationGroup` or disable some `widgets` or any other options and given that the dashboard is a simple filament `page`; The easiest solution would be to disable the default dashboard and create a new `page`:

```

php artisan filament:page MyCustomDashboardPage

```

then register the widgets you want from the **Available Widgets** list either in the `getHeaderWidgets()` or `getFooterWidgets()`. This page has been already created and you can find it in `\app\Filament\Pages\MyCustomDashboardPage.php` . Uncomment the widgets that you want in this page

#### BUT REMEMBER YOU HAVE TO GET GOOGLE CREDINTIALS FIRST OR AN ERROR WILL BE THROWN IN THE DASHBOARD PAGE

  

## LaraZeus

https://larazeus.com/docs/sky/v3/getting-started/installation

### Installation

```

composer require lara-zeus/sky

php artisan sky:install

```

  

## Filament menus

https://filamentphp.com/plugins/3x1io-tomato-menus

  

You can use the following code as the base for displaying the menu items on the frontend

```

@foreach (menu('header') as $item)

<a class="text-gray-500" href="{{ $item['url'] }}" @if($item['blank']) target="_blank" @endif>

<span class="flex justify-between">

@if(isset($item['icon']) && !empty($item['icon']))

<x-icon class="w-4 h-4 mx-2" name="{{ $item['icon'] }}"></x-icon>

@endif

{{ $item['title'] }}

</span>

</a>

@endforeach

```

  

## Filament API (Resource API Generator)

https://github.com/tomatophp/filament-api

  

### Usage

(https://github.com/tomatophp/filament-api#usage)

  

- You can generate API by add this trait to your resource pages

```

use TomatoPHP\FilamentApi\Traits\InteractWithAPI;

use \Filament\Resources\Pages\ListRecords;

  

class ListPosts extends ListRecords

{

use InteractWithAPI;

}

```

  

and that's it you can now access your API by `/api/{slug}`

  

we provide 5 methods:

  

- GET `/api/{slug}` to list all records `support searching by use search=`

- GET `/api/{slug}/{id}` to get single record

- POST `/api/{slug}` to create new record

- PUT `/api/{slug}/{id}` to update record

- DELETE `/api/{slug}/{id}` to delete record

  

### Custom your API

  

(https://github.com/tomatophp/filament-api#custom-your-api)

  

- You can customize your api by override this methods

```

// Use to return API JSON Resource on Index/Show/Store/Update

public static function getFilamentAPIResource(): ?string

{

return null;

}

  

// Use To Custom Your Route Middleware

public static function getFilamentAPIMiddleware(): array

{

return config('filament-api.default_middleware');

}

  

// Use To Change the Endpoint Slug

public static function getFilamentAPISlug(): ?string

{

return null;

}

```

  

## Others

- To change the filament panel site title, logo or favicon. Uncomment these lines in `AdminPanelProvider.php`

```

->brandName(__()) // SET THE NAME OF THE WEBSITE

->brandLogo(url()) // SET THE LOGO URL

->favicon(url()) // SET THE FAVICON URL

```

