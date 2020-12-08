<?php

namespace BaseTools\ActiveRecords;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActiveScope implements Scope
{
    public static $activate_scope = true;

    public static function ActivateScope($activate = true)
    {
        static::$activate_scope = $activate;
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
        if (static::$activate_scope) {
            $builder->where($model->getIsDisabledColumn(), '=', false);
        }
    }
}
