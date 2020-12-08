<?php

namespace BaseTools\ComplexSearch;

trait ComplexSearchRepoTrait
{
    public function complexSearch(array $request_all, $currentPage = null)
    {
        $query = $this->getModel();

        $query = $this->complexSearchQuery($query, $request_all);

        return $this->paginater($query, $currentPage);
    }

    public function complexSearchSingle(array $request_all)
    {
        $query = $this->getModel();

        $query = $this->complexSearchQuery($query, $request_all);

        return $query->firstOrFail();
    }

    /**
     * complexSearch function
     *
     *  [ 
     *      "searchable"=>[
     *          "operator",
     *          "value"
     *      ]
     *  ]
     */
    public function complexSearchQuery($query, array $request_all)
    {

        if (
            !method_exists(
                $this->getModel(),
                'searchables'
            )
        ) {
            throw new \Exception('searchables doesn\'t exist in model', 3007);
        }

        $searchables = $this->getModel()->searchables();

        foreach ($searchables as $searchable => $constraints) {

            if (!isset($request_all[$searchable])) {
                continue;
            }

            $operator = $constraints["operators"][0];

            $relationType = isset($constraints["relationTypes"]) ?
                $constraints["relationTypes"][0] : "whereHas";

            if (
                isset($request_all[$searchable]["relationType"])
                && isset($constraints["relationTypes"])
            ) {
                foreach ($constraints["relationTypes"] as $rType) {

                    if (strcmp($rType, $request_all[$searchable]["relationType"]) === 0) {
                        $relationType = $request_all[$searchable]["relationType"];
                    }
                }
            }

            if (isset($request_all[$searchable]["operator"])) {

                foreach ($constraints["operators"] as $ope) {
                    if (strcmp($ope, $request_all[$searchable]["operator"]) === 0) {
                        $operator = $request_all[$searchable]["operator"];
                    }
                }
            }

            if (!isset($request_all[$searchable]["value"])) {
                continue;
            }

            $value = $request_all[$searchable]["value"];

            $variable_name = $searchable;

            $this->searchValidator($variable_name = $searchable, $operator, $value);

            $modified_value = $value;

            if (isset($constraints["value_manipulator"])) {
                $modified_value = $constraints["value_manipulator"]($value);
            }

            $complexSearchable = explode(".", $variable_name);

            if (count($complexSearchable) == 1) {
                $query = $this->getBy($query, $variable_name, $operator, $modified_value);
                continue;
            }

            if (count($complexSearchable) >= 2) {


                $culumn_name = $complexSearchable[count($complexSearchable) - 1];
                unset($complexSearchable[count($complexSearchable) - 1]);

                $table = implode(".", $complexSearchable);

                if (
                    strcmp("has", $relationType) === 0 ||
                    strcmp("doesntHave", $relationType) === 0
                ) {
                    $query = $query->{$relationType}(
                        $table
                    );
                } else {
                    $query = $query->{$relationType}(
                        $table,
                        function ($sub_query) use ($culumn_name, $operator, $modified_value) {
                            $sub_query->where($culumn_name, $operator, $modified_value);
                        }
                    );
                }
            }
        }

        return $query;
    }

    public function searchValidator($variable_name, $operator, $value)
    {
        $searchables = $this->getModel()->searchables();

        $variable_rules = $searchables[$variable_name]["rules"];

        $this->validateCustomRules(
            $input = [$variable_name => $value],
            $rules = [$variable_name => $variable_rules],
            $messages = []
        );
    }

    public function getBy($query, $variableName, $operation, $value)
    {
        return $query->where($variableName, $operation, $value);
    }
}
