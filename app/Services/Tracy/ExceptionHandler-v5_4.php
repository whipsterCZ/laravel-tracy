<?php

/**
 * Larevel version 5.1
 * @author Daniel Kouba <whipstercz@gmail.com>
 */

namespace App\Services\Tracy;


use Exception;
use Illuminate\Foundation\Exceptions\Handler as BaseExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tracy\Debugger;

class ExceptionHandler extends BaseExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

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

        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } elseif ( !Debugger::$productionMode ) {
            if ( !app()->runningInConsole()) {
                return Debugger::exceptionHandler($e, true);
            }
        }

        return $this->prepareResponse($request, $e);
    }

}
