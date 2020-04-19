# Setup

## Install a new laravel project

```bash
laravel new project-name
```

## Create a database

In terminal 

```bash
mysql -u root
```

Alternatively you can use a GUI like sqlpro to create a db.

* Dont forget to change db name in ".env" file.

## Migrate db

```php
php artisan migrate
```

## Install passport:

```bash
composer require laravel/passport
```

The db needs to be migrated again

```php
php artisan migrate
```

Now install passport with the next command:

```php
php artisan passport:install
```

It will return two users, the first as credentials to oauth service and the second as a regular user
copy this credentials and save it for development ONLY.

Add a laravel passport HasTokens trait to your User model, * remember to import to class.

app/User.php

```php
<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    ...
}
```

Add passport routes in app/Providers/AuthServiceProvider.php

```php
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    ...

    public function boot()
    {
        $this->registerPolicies();
        Passport::routes();
    }
}
```

Change api auth config in config/auth.php in the guards section just change the api driver from token to passport

```php

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
```

Add passport middelware to the app level, go to app/Http/Kernel.php file and add 
the CreateFreshApiToken middleware from passport in middlewareGroups property on web key at the very bottom: 

```php
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
        ],

        'api' => [
            'throttle:60,1',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];
```

## Add Frontend with Vue, Vue router and TailwindCSS 

Install laravel ui package

```bash
composer require laravel/ui
```

Install Vue and auth scaffold

```php 
php artisan ui vue --auth
```

To get all changes you need to run npm install command:

```bash
npm install
```

And npm run dev to compile the changes

```bash
npm run dev
```

* I strongly suggest to use laravel mix file and add the hot reload feature
Go to webpack.mix.js and add the next line at the very buttom (change the dev url to yours project url):

```js
mix.browserSync('my-site.test')
```

Then run npm run watch command

```bash
npm run watch
```

It will install the dependencies to use the browser sync functunallity so now re run the watch command

```bash
npm run watch
```

It will open a browser tab with your current project and it will reload any changes.

## Create a single entry point to the app.

Create an AppController:

```php 
php artsan make:controller AppController
```

Add an index method to return the home view:

```php 
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index()
    {
        return view('home');
    }
}
```

Then edit the web routes on routes/web.php:

```php
<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('{any}', 'AppController@index')->where('any', '.*')->middleware('auth')->name('home');
```

The last Route method add a rule to catch all requests and redirect to the AppController to return the home view, 
but it adds a middleware auth to prevent access without previous authentication.

## Make changes to work with Vue and Vue Router

Delete the Example component in resources/js/components 

Creates an App component in this directory:

```vue
<template>
    <dir>
        <h1>App</h1>
        <router-view></router-view>
    </dir>
</template>

<script>
export default {
    name: 'App',
}
</script>

<style>

</style>
```

Then go to your home view and add the App component in resources.views/home.blade.php:

```php
@extends('layouts.app')

@section('content')
    <App></App>
@endsection 
```

Creates a view folder on resources/js/views 

and add a view as Start.vue file 

```vue
<template>
    <div>
        <h1>Start</h1>
    </div>
</template>

<script>
export default {
    name: 'Start',
}
</script>

<style>

</style>
```

Install Vue Router

```bash
npm install vue-router --save-dev
```

Create a router file that allows VueRouter to handle all the routes as SPA

create a file in resources/js/router.js and add the next code:

```js
import Vue from 'vue'
import VueRouter from 'vue-router'
import Start from './views/Start'

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        { path: '/', name: 'home', component: Start },
    ]
})

```

Now we need to edit app.js file to load the component on resources/js/app.js

```js
import Vue from 'vue'
import router from './router'
import App from './components/App'

require('./bootstrap')

const app = new Vue({
    el: '#app',
    components: {
        App,
    },
    router
})

```

## Delete all bootstrap related 
* IMPORTANT This step is only if you are not using bootstrap as css framework

go to package.json file and delete the next files in the dev dependencies key, (at the very bottom of the file)

popper.js
bootstrap
jquery

Now your package.json file in dev dependencies key will look like this:

```json
    "devDependencies": {
        "axios": "^0.19",
        "browser-sync": "^2.26.7",
        "browser-sync-webpack-plugin": "^2.0.1",
        "cross-env": "^7.0",
        "laravel-mix": "^5.0.1",
        "lodash": "^4.17.13",
        "resolve-url-loader": "^2.3.1",
        "sass": "^1.20.1",
        "sass-loader": "^8.0.0",
        "vue": "^2.5.17",
        "vue-router": "^3.1.6",
        "vue-template-compiler": "^2.6.10"
    }
```

Now the dependencies are no more in this file, 
but it will load to avoid this go to resources/sass/app.scss and clean all the file including bootstrap imports
the app.scss must be totally empty and save
* If you are going to use TailwindCSS or any other CSS framework or SASS to compile CSS Dont! delete this app.scss file, it will be helpful.
* You cannot delete this file because the webpack.mix.js file load it on every npm run command
if you are really interested in delete this file (not recommended) and you will not use sass or any other css framework, you can delete the reference of this file in webpack.mix.js file and then you can delete it, without problems.

Now delete the -variables.scss file this file can be deleted without any problem.

To avoid load old bootstrap javascript dependencies go to resources/js/bootstrap.js file and delete the next imports:

```js
try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}
```

If you like clean all comments in this file and it should look like this:

```js
window._ = require('lodash');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

```

Pretty much clear not? 
Now to get all changes we need to compile all again with npm run dev
(if you are using browser sync feature of laravel mix this is not necessary because it rebuild and reload on every change)

```bash
npm run dev
```

open your project it would show the login screen at first now, register and then login,
you would be able to see your components content.

* If you dont see your components content open the browser console it would show errors if exists, 
and open vue dev tools extension to be sure that VueJS is loading correctly.

## Redirect correctly to "/" after login

Now is almost done but, the login scaffold that ships with laravel ui package authentication redirect by default to "/home",
luckly now its really simple to change this redirect:

In laravel 6 and below:

go to app/Http/Controllers/Auth here are the classes that handles the authentication, just open the files and change the property 

```php
protected $redirectTo = '/home';
```

To:

```php
protected $redirectTo = '/';
```

Easy right? ummm let me dissapoint you in Laravel 7 is even much easier:

In Laravel 7

Open the RouteServiceProvider in app/Providers/RouteServiceProvider.php and change the redirectTo property:

```php 
public const HOME = '/home';
```

This const is reference in all classes that handles the authntication in app/Http/Controllers/Auth so its pretty convinient.


## Adding TailwindCSS

Install Tailwind

```bash
npm install tailwindcss
```

Add Tailwind directives to resources/sass/app.scss file:

```scss
@tailwind base;

@tailwind components;

@tailwind utilities;
```

Add Tailwind config file:

```bash
npx tailwindcss init
```

Then add tailwindcss to laravel mix compilation process, go to webpack.mix.js and edit the file:

```js
const mix = require('laravel-mix')

const tailwindcss = require('tailwindcss')

mix.js('resources/js/app.js', 'public/js')
    mix.sass('resources/sass/app.scss', 'public/css')
        .options({
            processCssUrls: false,
            postCss: [ tailwindcss('./tailwind.config.js') ],
        })

mix.browserSync('your-app.test')

```

If you are using a different file than resources/sass/app.scss file just change the reference to this file, 
the same for tailwindcss config file, if you change the location of this file just edit the tailwindcss() param.

the mix.browserSync method is totally optional but I prefer this to get hot reload in development.

Now just compile again even if you are using browser sync feature of Laravel mix sometimes this is necessary:

Stop the terminal with: "control + c"

Then run:

```bash
npm run watch
```

Finally your project is ready to development with Laravel, Vue, VueRouter and TailwindCSS.

you can edit your views and add tailwind classes to check that all works fine.
