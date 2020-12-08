<?php

namespace BaseTools\ActiveRecords;

trait ActiveRecords
{

    /**
     * Boot the Active Records trait for a model.
     *
     * @return void
     */
    public static function bootActiveRecords()
    {
        static::addGlobalScope(new ActiveScope);
    }


    /**
     * Get the name of the "is_disabled" column.
     *
     * @return string
     */
    public function getScopeVariablesColumn()
    {
        return defined('static::scope_variables') ? static::scope_variables : [];
    }
}
