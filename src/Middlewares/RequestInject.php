<?php

namespace BaseTools\Middlewares;

use Closure;
use BaseTools\ComplexSearch\injectionTrait;

class RequestInject
{
    use injectionTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$injectedArray)
    {
        // Because laravel cuts at ,
        if (count($injectedArray)) {
            $injectedArray = implode(",", $injectedArray);
            $injectedArray = $this->injectRouteParameters($request, $injectedArray);
            $injectedArray = $this->injectAuthParam($request, $injectedArray);
            $injectedArray = $this->injectRequestParam($request, $injectedArray);
        }

        $injectedArray = $this->discardRequestIfNotFound($injectedArray);

        $request->merge(json_decode($injectedArray, true));

        return $next($request);
    }
}
