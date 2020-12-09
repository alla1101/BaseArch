<?php

namespace BaseTools\ComplexSearch;

trait ComplexSearchModelTrait
{

    /**
     * Searchables
     * 
     * call parent function, and add other searchables using $other_searchables field
     *
     * @param array $other_searchables
     * @param array $relation_names
     * @param array $relation_searchables
     * @return array
     */
    public function searchables($other_searchables = [], $relation_names = [], $relation_searchables = [])
    {

        $searchables = [
            "id" => [
                "operators" => ["=", "!="],
                "rules" => "integer|min:1"
            ], "is_disabled" => [
                "operators" => ["=", "!="],
                "rules" => "boolean"
            ]
        ];

        $searchables = array_merge($searchables, $other_searchables);

        foreach ($relation_names as $relation_name) {
            $searchables = $this->addToSearchables(
                $searchables,
                $relation_searchables[$relation_name],
                $relation_name
            );
        }

        return $searchables;
    }

    private function addToSearchables($original_searchables, $to_be_added, $items_relation_name)
    {
        $new_searchables = $original_searchables;

        foreach ($to_be_added as $searchable => $constraints) {
            $new_searchables[$items_relation_name . "." . $searchable] = $constraints;
        }

        return $new_searchables;
    }
}
