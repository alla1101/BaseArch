<?php

namespace BaseTools\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class MultipleRepoOperationController extends Controller
{

    private static $Repos = [];

    private $request_all = [];

    public static function setRepositories($Repos)
    {
        foreach ($Repos as $Repo) {
            static::$Repos[] = new $Repo;
        }
    }

    private function getRepositories()
    {
        return static::$Repos;
    }

    private function parseRequest($var_name, $model = null, $old_var_name = null)
    {

        if (!is_null($old_var_name) && !is_null($model)) {
            $this->request_all["relation_key." . $old_var_name] = $model->id;
        }

        $request_for_repo = [];

        foreach ($this->request_all as $key => $value) {
            // If Normal key based on var_name or Relation key
            $arr = preg_split("/^" . $var_name . "./", $key);
            if (count($arr) == 2) {
                // Add to Request
                $request_for_repo[$arr[1]] = $value;
                continue;
            }

            $arr = explode("relation_key.", $key);
            if (count($arr) > 1) {
                // Add to Request
                $request_for_repo[$arr[1]] = $value;
                continue;
            }
        }

        return $request_for_repo;
    }

    private function getVarName($model_name)
    {
        $arr = explode("\\", "" . get_class($model_name));
        $model_name = $arr[count($arr) - 1];
        return $var_name = Str::snake($model_name);
    }

    public function store(Request $request)
    {
        $model = null;
        $var_name = null;
        $old_var_name = null;
        $this->request_all = $request->all();

        // Surround By DB::transaction and roleback
        // Store All Based On Model.variable 
        $repositories = $this->getRepositories();

        DB::beginTransaction();

        try {

            foreach ($repositories as $repo) {

                $model_name = ((new $repo)->getModel()); // Parse into name for variable
                $var_name = $this->getVarName($model_name);

                $request_for_repo = $this->parseRequest($var_name, $model, $old_var_name);

                $model = $repo->add(
                    $request_for_repo
                );
                $old_var_name = $var_name . "_id";
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        // End Surround DB transaction

        // Get Data From Main Repo ( Last Repo )

        return [
            "status" => true,
            "data"  => $model
        ];
    }

    public function put(Request $request)
    {
        $model = null;
        $var_name = null;
        $old_var_name = null;
        $this->request_all = $request->all();

        // Surround By DB::transaction and roleback
        // Store All Based On Model.variable 
        $repositories = $this->getRepositories();

        DB::beginTransaction();

        try {

            foreach ($repositories as $repo) {

                $model_name = ((new $repo)->getModel()); // Parse into name for variable
                $var_name = $this->getVarName($model_name);

                $request_for_repo = $this->parseRequest($var_name, $model, $old_var_name);

                $repo->update(
                    $request_for_repo["id"],
                    $request_for_repo
                );

                $model = $repo->getById($request_for_repo["id"]);

                $old_var_name = $var_name . "_id";
            }
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        // End Surround DB transaction

        // Get Data From Main Repo ( Last Repo )

        return [
            "status" => true,
            "data"  => $model
        ];
    }

    public function test()
    {
        return "Multiple Repositories Operation Controller";
    }
}
