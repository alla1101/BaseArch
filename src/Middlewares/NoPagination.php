<?php

namespace BaseTools\Middlewares;

use Closure;

class NoPagination
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request->merge(["page" => "InternalAPICall"]);
        return $next($request);
    }
}
