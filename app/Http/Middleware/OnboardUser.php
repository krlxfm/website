<?php

namespace KRLX\Http\Middleware;

use Closure;

class OnboardUser
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
        $user = $request->user();
        if (! ends_with($user->email, '@carleton.edu')) {
            return $next($request);
        }

        if ($user->phone_number == null) {
            $request->session()->put('url.intended', $request->path());

            return redirect()->route('legal.onboard');
        }

        return $next($request);
    }
}
