<?php

namespace App\Services\Tracy;

use Closure;
use Tracy\Debugger;

class Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @tracySkipLocation
     */
    public function handle($request, Closure $next)
    {
	    try {
		    return $next($request);
	    } catch (\Exception $e) {

		    //pokusím vypsat výjimku
		    if ( Debugger::$productionMode )  {
			    Debugger::log($e);
		    } else {
			    Debugger::exceptionHandler($e);
		    }

		    //pokud je debugger vypnutý let it bubble
		    throw $e;
	    }

    }
}
