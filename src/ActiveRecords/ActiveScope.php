<?php

namespace BaseTools\ActiveRecords;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActiveScope implements Scope
{
    public static $injectedVariables = [];

    public static function ActivateScope($injectedVariables = [])
    {
        static::$injectedVariables = $injectedVariables;
    }
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $pattern = "/^(" . implode("|", static::$injectedArray) . ")$/";

        $scope_variables = $model->getScopeVariablesColumn();

        $variables_to_remove = preg_grep($pattern, $scope_variables);

        $remaining_scope_variables = array_diff_assoc($scope_variables, $variables_to_remove);

        foreach ($remaining_scope_variables as $scope_variable) {
            $builder->where($scope_variable, '=', false);
        }
    }
}
