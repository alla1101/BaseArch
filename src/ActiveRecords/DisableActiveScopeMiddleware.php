<?php

namespace BaseTools\ActiveRecords;

use Closure;
use Illuminate\Database\Eloquent\Model;

class DisableActiveScopeMiddleware
{
    public function handle($request, Closure $next)
    {
        ActiveScope::ActivateScope(false);
        return $next($request);
    }
}
