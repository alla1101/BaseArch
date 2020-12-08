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
    public function getIsDisabledColumn()
    {
        return defined('static::IS_DISABLED') ? static::IS_DISABLED : 'is_disabled';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedISDisabledColumn()
    {
        return $this->qualifyColumn($this->getIsDisabledColumn());
    }
}
