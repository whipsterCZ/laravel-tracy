<?php

/**
 *
 * Larevel version 5.2
 * @author Daniel Kouba <whipstercz@gmail.com>
 */

namespace App\Services\Tracy;

use Auth;
use Exception;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exception\HttpResponseException;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as IlluminateExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tracy\Debugger;

class ExceptionHandler extends IlluminateExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
        if ($this->shouldReport($e)) {
            //If larevel-bugsnag is present
            if (app()->bound('bugsnag') && app()->environment(config('bugsnag.notify_release_stages'))) {

                $meta = null;
                if (Auth::check() && ($user = Auth::user())) {
                    $meta = [];
                    if ($user->name) {
                        $meta['user']['name'] = $user->name;
                    }
                    if ($user->full_name) {
                        $meta['user']['name'] = $user->full_name;
                    }
                    if ($user->role) {
                        $meta['user']['role'] = $user->role;
                    }
                    $meta['user']['email'] = $user->email;
                }

                app('bugsnag')->notifyException($e, $meta, "error");
            }

            if (Debugger::$productionMode) {
                Debugger::log($e);
            }
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

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            return $e->getResponse();
        } elseif ( !Debugger::$productionMode ) {
            if ( !$this->app->runningInConsole()) {
                return Debugger::exceptionHandler($e, true);
            }
        }
        return parent::render($request,$e);

    }

}
