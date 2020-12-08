<?php

namespace BaseTools\Controllers;

trait WithFractalController
{

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
}
