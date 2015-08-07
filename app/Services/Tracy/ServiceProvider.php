<?php

namespace App\Services\Tracy;

require_once('shortcuts.php');

use Illuminate\Support\Facades\Config;
use Tracy\Debugger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

		if (!$this->app->runningInConsole()) {

//		    Config::set('app.debug', false);
			Debugger::$logDirectory = realpath(__DIR__.'/logs');
			Debugger::$showLocation = true;

			//enable only in case pf development - true == production
			$isProduction =  !env('APP_DEBUG',false);

			Debugger::enable( $isProduction );
		}

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerMiddleware('App\Services\Tracy\Middleware');
	}

	/**
	 * Register the Debugbar Middleware
	 *
	 * @param  string $middleware
	 */
	protected function registerMiddleware($middleware)
	{
		$kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
		$kernel->pushMiddleware($middleware);
	}
}
