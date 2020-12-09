<?php

namespace BaseTools\Middlewares;

use Closure;

class TransformerIncludes
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(
        $request,
        Closure $next,
        $transformerName = "\BaseTools\Transformers\NoTransformer",
        ...$injectedIncludes
    ) {
        $stat = null;
        ($result = $next($request));
        $result = $result->original;

        if ($this->isArray($result) && $this->isNotOperation($result)) {
            return $result;
        }

        if ($this->isArray($result) && $this->isOperation($result)) {
            $stat = $result["status"];
            $result = $result["data"];
        }

        if (!$this->isMany($result)) {
            $Response = fractal()
                ->item($result)
                ->transformWith(new $transformerName)
                ->parseIncludes($injectedIncludes);
        }

        if ($this->isMany($result)) {

            $Response = fractal()
                ->collection($result)
                ->transformWith(new $transformerName)
                ->parseIncludes($injectedIncludes);
        }

        if ($this->isPaginated($result)) {
            $Response = $this->AfterFractalTransformers($result, $Response);
        } else {

            $Response = $Response->toArray();
        }

        if (!is_null($stat)) {
            $Response = $Response["data"];
            $Response = ["status" => $stat, "data" => $Response];
        }

        return $Response;
    }

    private function isNotOperation($result)
    {
        return !($this->isOperation($result));
    }

    private function isOperation($result)
    {
        return isset($result["status"]) && isset($result["data"]);
    }

    private function AfterFractalTransformers($NormalData, $Transformed)
    {
        $Transformed = $Transformed->createData()->toArray();
        $NormalData = $NormalData->toArray();
        unset($NormalData["data"]);
        $Transformed["meta"] = $NormalData;
        $Transformed["meta"] = $this->paginationMapper($Transformed["data"], $NormalData);
        return $Transformed;
    }

    public function paginationMapper($data, $meta_r)
    {

        $meta = [
            "pagination" => [
                "count" => count($data),
                "total" => $meta_r["total"],
                "per_page" => $meta_r["per_page"],
                "current_page" => $meta_r["current_page"],
                "total_pages" => $meta_r["last_page"],
                "links" => []
            ]
        ];

        if (!is_null($meta_r["next_page_url"])) {
            $meta["pagination"]["link"]["next"] = $meta_r["next_page_url"];
        }

        return $meta;
    }

    private function isArray($result)
    {
        return is_array($result);
    }

    private function isPaginated($result)
    {
        return $result instanceof \Illuminate\Contracts\Pagination\Paginator;
    }

    private function isMany($result)
    {
        return $result instanceof \Illuminate\Database\Eloquent\Collection || $result instanceof \Illuminate\Contracts\Pagination\Paginator;
    }
}
