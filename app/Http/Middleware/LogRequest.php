<?php

namespace KRLX\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $string = $request->user() ? $request->user()->email : 'guest';
        $string .= ", {$request->ip()}: {$request->method()} {$request->path()}";

        Log::info($string);

        return $next($request);
    }
}
