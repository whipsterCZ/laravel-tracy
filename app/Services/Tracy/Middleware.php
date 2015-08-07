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


		    if ( Debugger::$productionMode )  {
			    //Loging exception
			    Debugger::log($e);
		    } else {
			    //Rendering error page for exception
			    Debugger::exceptionHandler($e);
		    }

		    //let it bubble
		    throw $e;
	    }

    }
}
