<?php

namespace App\Services\Tracy;

require_once('shortcuts.php');

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Tracy\Debugger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		Debugger::$logDirectory = storage_path('logs');
		//Debugger::$email = 'admin@example.com';
		Debugger::$showLocation = true;
		Debugger::$strictMode = true;

		//Enable only in debug mode - (DEV)
		$debugMode = env('APP_DEBUG',false);
		$productionMode = !$debugMode;
		Debugger::enable( $productionMode );

		//register own ExceptionHandler
		$this->app->bind(ExceptionHandlerContract::class, ExceptionHandler::class);
	}

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

	}

}
