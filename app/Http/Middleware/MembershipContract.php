<?php

namespace KRLX\Http\Middleware;

use Closure;
use KRLX\Term;

class MembershipContract
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
        $term = null;
        if ($request->route()->hasParameter('term')) {
            $term = $request->route()->parameter('term');
        } elseif ($request->route()->hasParameter('show')) {
            $term = $request->route()->parameter('show')->term;
        } else {
            $term = Term::orderByDesc('on_air')->get()->first();
        }

        if (! $request->user()->points()->where([['status', '!=', 'none'], ['term_id', $term->id]])->first() and !$term->off_air->isPast()) {
            $request->session()->put('url.intended', $request->path());
            $request->session()->put('term', $term->id);

            return redirect()->route('legal.contract');
        }

        return $next($request);
    }
}
