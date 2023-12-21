# Laravel App Version Manager

## Installation

```shell
composer require wamesk/laravel-app-version-manager
```

Next step is to publish all translations, migrations and configurations. So you can change them to fit your needs.

```shell
php artisan vendor:publish --provider="Wame\LaravelAppVersionManager\LaravelAppVersionManagerProvider"
```

## Configuration

### Translations

You can find translations here.

You can see now it has limited translations being the folders like "en"

You can create new translations by creating another folder with version-messages.php inside.

Also, you can edit existing translations if you need.

```
resourses/
    └── lang/
        └── vendor/
            └── laravel-app-version/
                ├── sk/
                │   └── version-messages.php
                ├── cz/
                │   └── version-messages.php
                └── en/
                    └── version-messages.php
```

### Migrations

You can find published migrations in migrations folder.

There isn't much to change there, but you might want to change id type if you are using different from ulid.

### Config

You can find published config in config folder.

In there you can find these keys that have different purposes, you can see what they are for here.

You are free to change them if you need to.

**app_name**

This key will determine what app name will be used in response messages.

**route.prefix**

By default, this is api/v1 but if you are using perhaps api/v2 or maybe not versioning your api endpoints.
You can change this to api or empty string.

## Usage

### Header

This package is about app version. So it heavily focuses on api endpoints.

For this package to work you need to be sending **app-version** header in each request you cover with middleware.

### Middleware

This middleware needs to cover every api endpoint you are using in your app.

Like mentioned above you need to be sending **app-version** header with which it will be working.

How it works: This middleware is pretty simple, takes **app-version** header, finds version in db,
then it checks if version has status deprecated. If so it returns 426 response.

#### Usage

Here is an example how to use this middleware. You can also register it globally,
but in this example we will cover only route generating usage.

```php
use Wame\LaravelAppVersionManager\Http\Middleware\DeprecatedVersionCheckMiddleware;

Route::group(['middleware' => DeprecatedVersionCheckMiddleware::class], function () {
    \Illuminate\Support\Facades\Route::post('login', [LoginController::class, 'login']);
});
```

### Api Endpoint

This package also comes with api endpoint.

Route url is .../app-version-check, configurable in config.

If the version sent in header **app-version** is old or up to date. Endpoint will respond with 200 status code.

There will be two parameters in response:

**message** - Basic translated message that can be used to display in app.

**update** - Boolean parameter that tells if there is newer versions of app.

### Version History

Every change to app version using model will be recorded in app_version_history table.
