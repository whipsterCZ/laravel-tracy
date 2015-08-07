<?php

namespace App\Services\Tracy;

require_once('shortcuts.php');

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
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

		if (!$this->app->runningInConsole() && !Request::ajax() ) {

			Debugger::$logDirectory = realpath(__DIR__.'/logs');
			Debugger::$showLocation = true;

			//Enable only in case pf development - true == production
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
