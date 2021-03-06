<?php

namespace BaseTools\Middlewares;

use Closure;
use BaseTools\ComplexSearch\injectionTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use BaseTools\ComplexSearch\ComplexSearchControllerTrait;

class ConditionalActionFail
{
    use injectionTrait, ComplexSearchControllerTrait;

    private $repo;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $repo, ...$injectedQuery)
    {
        $this->repo = new $repo;

        try {
            // Because laravel cuts at ,
            if (count($injectedQuery)) {
                $injectedQuery = implode(",", $injectedQuery);
                $injectedQuery = $this->injectRouteParameters($request, $injectedQuery);
                $injectedQuery = $this->injectAuthParam($request, $injectedQuery);
                $injectedQuery = $this->injectRequestParam($request, $injectedQuery);
            }

            $conditionalQuery = json_decode($injectedQuery, true);

            // Single Search Start
            $request_all = $this->searchableRequest($this->Repo(), $conditionalQuery);

            $query = $this->Repo()->complexSearchSingle($request_all);

            throw new \Exception('Can\'t continue this operation', 9999);
        } catch (ModelNotFoundException $exception) {
        }

        // Finish Single Search
        return $next($request);
    }

    private function Repo()
    {
        return $this->repo;
    }
}
