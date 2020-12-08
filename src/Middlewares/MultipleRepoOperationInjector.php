<?php

namespace BaseTools\Middlewares;

use Closure;
use BaseTools\Controllers\MultipleRepoOperationController;

class MultipleRepoOperationInjector
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    public function handle($request, Closure $next, ...$injectedRepositories)
    {

        MultipleRepoOperationController::setRepositories($injectedRepositories);
        return $next($request);
    }
}
