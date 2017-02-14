Tracy Debugger support for Laravel
=====================================

This service provider adds the powerful [Tracy debug tool](https://github.com/nette/tracy) to the Laravel 5 framework.

[![Nette Tracy screenshot](http://nette.github.io/tracy/images/tracy-exception.png)](http://nette.github.io/tracy/tracy-exception.html)

**Why is Tracy better than the laravel (Symfony) build-in debugger?**

* Exception stack trace contains values of all method arguments.
* Request & Response & Server environment information is displayed on the error page.
* The whole error page with full stack trace can be easily stored to some directory as HTML file (useful on production mode).
* Webmaster can be notified by email about errors that occured on the site.

See [full Tracy docs](https://github.com/nette/tracy) and [sample error page](http://nette.github.io/tracy/tracy-exception.html).

Tracy is a part of the [Nette Framework](http://nette.org/).

Installation
------------

1) Add **Tracy** to your **composer.json**
~~~~~ json
"require": {
    "tracy/tracy": "^2.3"
}
~~~~~

2) Add source code to your existing App - directories should **match service Namespace**
~~~~~ php
app/Services/Tracy/
~~~~~


3) register service provider in your **config/app.php**
~~~~~ php
'providers' => [
	...,
	App\Services\Tracy\ServiceProvider::class,
]
~~~~~


Configuration
-------------
This is it! No configuration needed.
If you need configure Tracy or Laravel Exception handler @see `Tracy/ServiceProvider.php` 
There are several versions of ExceptionHandlers for different Laravel Version - default is 5.2


Bugsnag integration
-------------------
Tracy service automatically utilize **Bugsnag error handling** if `bugsnag-laravel` package is present


Logging
-------------
Service recognize application environment using `env('APP_DEBUG')` 

If `APP_DEBUG is false` or `Request()->ajax() is true` service log your Exceptions to directory
~~~~~ php
/storage/logs/
~~~~~

TroubleShooting
------------
If you are sending JSON with TracyDebugBar rendered, you have probably created JSON Response wrong way.

~~~~~ php
Route::get('json',function(){
	$user = \App\Models\User::first();
	return $user; //Correct ->toJson() will be invoked and JSON Response will be created
	
	$json = $user->toJson();
	return $json; //Wrong - it creates Text/Plain Response
	
	return \Response::json($json); //This is OK, but note that your JSON will be ESCAPED
});
~~~~~


