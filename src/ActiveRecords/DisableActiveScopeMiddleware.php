<?php

namespace BaseTools\ActiveRecords;

use Closure;
use Illuminate\Database\Eloquent\Model;

class DisableActiveScopeMiddleware
{
    public function handle($request, Closure $next, ...$injectedVariables)
    {
        ActiveScope::ActivateScope($injectedVariables);
        return $next($request);
    }
}
