<?php

namespace App\Services\Tracy;

use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Tracy\Debugger;

class Handler extends ExceptionHandler
{

	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		HttpException::class,
		ModelNotFoundException::class
	];

	/**
	 * @var Application
	 */
	protected $app;

	function __construct(Application $app, LoggerInterface $log)
	{
		parent::__construct($log);
		$this->app = $app;
	}

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
		//If larevel-bugsnag is present
		if (app()->bound('bugsnag')) {

			$meta = null;
			if(Auth::check() && ($user = Auth::user())) {
				$meta = [];
				if( $user->name ) {
					$meta['user']['name'] = $user->name;
				}
				if( $user->full_name ) {
					$meta['user']['name'] = $user->full_name;
				}
				if( $user->role ) {
					$meta['user']['role'] = $user->role;
				}
				$meta['user']['email'] = $user->email;
			}

			app('bugsnag')->notifyException($e, $meta, "error");
		}

		if (Debugger::$productionMode ) {
			Debugger::log($e);
		}
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $e
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $e) {

		if ( !Debugger::$productionMode ) {
			if ( !$this->app->runningInConsole()) {
				return Debugger::exceptionHandler($e, true);
			}
		}
		return parent::render($request,$e);

	}

}
