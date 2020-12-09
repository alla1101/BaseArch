<?php

namespace BaseTools\Controllers;

use Illuminate\Http\Request;
use BaseTools\Controllers\BaseCodeForController;
use BaseTools\ComplexSearch\ComplexSearchControllerTrait;

trait ModelBasedController
{
    use BaseCodeForController, ComplexSearchControllerTrait;


    public function index(Request $Request)
    {
        $currentPage = $this->HandlePage($Request->page);

        $query = $this->Repo()->getAll($currentPage);

        return $query;
    }

    public function toggle(int $id, String $name)
    {
        $Response = $this->Repo()->toggle($id, $name);

        $model = $this->Repo()->getById($id);

        return [
            "status" => true,
            "data" => $model
        ];
    }

    public function getById($id)
    {
        $model = $this->Repo()->getById(
            $id
        );

        return $model;
    }

    public function store(Request $request)
    {
        $model = $this->Repo()->add(
            $request->all()
        );

        return [
            "status" => true,
            "data" => $model
        ];
    }

    public function put(Request $request, $id)
    {
        $status = $this->Repo()->update(
            $id,
            $request->all()
        );

        $model = $this->Repo()->getById($id);

        return [
            "status" => $status,
            "data"    => $model
        ];
    }

    public function putBySingleSearch(Request $request)
    {
        $request_all = $request->all();

        $singleSearchRequest = $request_all["singleSearch"];

        $request_all = $this->searchableRequest($this->Repo(), $singleSearchRequest);
        $query = $this->Repo()->complexSearchSingle($request_all);
        $model_id = $query->id;

        $status = $this->Repo()->update(
            $model_id,
            $request_all
        );

        $model = $this->Repo()->getById($model_id);

        return [
            "status" => $status,
            "data"    => $model
        ];
    }

    public function deleteBySingleSearch(Request $request)
    {

        $request_all = $request->all();

        $singleSearchRequest = $request_all["singleSearch"];

        $request_all = $this->searchableRequest($this->Repo(), $singleSearchRequest);
        $query = $this->Repo()->complexSearchSingle($request_all);
        $model_id = $query->id;

        return $this->delete($model_id);
    }

    public function delete($id)
    {
        $model = $this->Repo()->delete(
            $id
        );

        return [
            "status" => $model
        ];
    }

    private function Repo()
    {
        return $this->Repository;
    }

    public function test()
    {
        return $this->Repo()->test();
    }
}
