<?php

namespace App\Services\Tracy;

require_once('shortcuts.php');

use App\Services\Tracy\Panels\ConnectionPanel;
use App\Services\Tracy\Panels\RequestPanel;
use App\Services\Tracy\Panels\RoutingPanel;
use App\Services\Tracy\Panels\SessionPanel;
use App\Services\Tracy\Panels\UserPanel;
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
		$this->app->bind(ExceptionHandlerContract::class, Handler::class);
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
