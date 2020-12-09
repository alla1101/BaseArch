<?php

namespace BaseTools\ComplexSearch;

use Illuminate\Http\Request;

trait ComplexSearchControllerTrait
{

    public function generalSearch(Request $request)
    {
        $currentPage = $this->HandlePage($request->page);

        $request_all = $this->searchableRequest($this->Repo(), $request->all());

        $query = $this->Repo()->complexSearch($request_all, $currentPage);

        return $query;
    }

    public function SingleSearch(Request $request)
    {
        $request_all = $this->searchableRequest($this->Repo(), $request->all());
        $query = $this->Repo()->complexSearchSingle($request_all);

        return $query;
    }

    public function searchableRequest($repo, array $request_all)
    {
        $request_arr = [];

        if (
            !method_exists(
                $repo->getModel(),
                'searchables'
            )
        ) {
            throw new \Exception('searchables doesn\'t exist in model', 3007);
        }

        $searchables = $repo->getModel()->searchables();

        foreach ($searchables as $searchable => $constraints) {

            if (!isset($request_all[$searchable])) {
                continue;
            }

            if (!isset($request_all[$searchable]["value"])) {
                continue;
            }

            $request_arr[$searchable]["value"] = $request_all[$searchable]["value"];

            if (isset($request_all[$searchable]["operator"])) {
                $request_arr[$searchable]["operator"] = $request_all[$searchable]["operator"];
            }

            if (isset($request_all[$searchable]["relationType"])) {
                $request_arr[$searchable]["relationType"] = $request_all[$searchable]["relationType"];
            }
        }

        return $request_arr;
    }
}
