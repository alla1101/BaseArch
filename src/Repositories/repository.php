<?php

namespace BaseTools\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use BaseTools\ComplexSearch\ComplexSearchRepoTrait;

trait repository
{

    use ComplexSearchRepoTrait;

    private $orderBy = "created_at";
    /** Pagination Limit in each page */
    private $page_limit = 12;

    public function __construct($orderBy = null)
    {
        $this->parent_construct($orderBy);
    }

    private function parent_construct($orderBy = null)
    {
        $attributes = \Schema::getColumnListing(($this->getModel())->getTable());

        if (!is_null($orderBy)) {
            $this->setOrderBy($orderBy);
        }

        if (is_null($orderBy) && !in_array("created_at", $attributes, true)) {
            $this->orderBy = null;
        }
    }

    public function getModel()
    {
        return new static::$model;
    }

    /**
     * Validates the input according to the rules.
     *
     * @param array $input input data.
     * @param array $rules rules of eloquent model.
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $input)
    {

        $this->validateCustomRules(
            $input,
            $this->getModelRules(),
            $this->custom_val_message()
        );
    }

    private function custom_val_message()
    {
        if (\App::isLocale('en')) {
            return [
                'required_if' => 'The :attribute field is required.'
            ];
        }
        if (\App::isLocale('ar')) {
            return [
                'required_if' => ':attribute مطلوب.|:attribute مطلوبة.'
            ];
        }
    }

    public function validateCustomRules(array $input, array $rules, array $messages = [])
    {

        Validator::make(
            $input,
            $rules,
            $messages
        )->validate();
    }

    private function canBeOrdered()
    {
        return !is_null($this->orderBy);
    }

    public function setOrderBy(string $orderBy)
    {
        $attributes = \Schema::getColumnListing(($this->getModel())->getTable());

        if (!in_array($orderBy, $attributes)) {
            throw new \Exception($orderBy . ' Doesn\'t Exist', 3007);
        }

        $this->orderBy = $orderBy;
    }

    /**
     * Paginates the output if the currentpage is set to a numerical value.
     *
     * @param Builder $query Eloquent query builder.
     * @param int $currentPage page number for pagination.
     * @return Illuminate\Contracts\Pagination\Paginator|Illuminate\Database\Eloquent\Collection
     */
    public function paginater($query, $currentPage)
    {
        if ($this->canBeOrdered()) {
            $query = $query->orderBy($this->orderBy, 'desc');
        }

        if (!is_numeric($currentPage)) {
            return $query->get();
        }

        \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        return $query->Paginate($this->page_limit);
    }

    /**
     * Get eloquent model Basic rules
     *
     * @return array 
     */
    public function getModelRules()
    {

        return $this->getModel()->rules;
    }

    /**
     * Adds new cpe login credentials.
     *
     * @param array $request_all data to be used as input.
     * @throws \Illuminate\Validation\ValidationException
     * @return Illuminate\Database\Eloquent\Model
     */
    public function add(array $request_all)
    {

        $this->validate(
            array_merge(
                $request_all,
                ['is_create' => 'y']
            )
        );

        if (
            method_exists(
                $this->getModel(),
                'getDynamicRulesForAdd'
            )
        ) {
            $this->getModel()->createCustomMessage();

            $this->validateCustomRules(
                $request_all,
                $this->getModel()->getDynamicRulesForAdd(
                    static::$model,
                    $request_all
                )
            );
        }

        $elIns = $this->getModel()->create($request_all);

        return $elIns;
    }

    /**
     * edit existing value to eloquent model.
     *
     * @param array $request_all data to be used as input.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @return boolean
     */
    public function update(int $id, array $request_all)
    {

        $model = $this->getModel()->findOrFail($id);

        //$request_all=array_diff($request_all,$model->toArray());

        $this->validate(
            $request_all
        );

        if (
            method_exists(
                $this->getModel(),
                'getDynamicRulesForUpdate'
            )
        ) {

            $this->getModel()->createCustomMessage();

            $this->validateCustomRules(
                $request_all,
                $this->getModel()->getDynamicRulesForUpdate(
                    $this->getById($id),
                    $request_all
                )
            );
        }

        return $model->update($request_all);
    }

    /**
     * Get All Values
     *
     * @param int|null $currentPage page number for pagination.
     * @return Illuminate\Contracts\Pagination\Paginator|Illuminate\Database\Eloquent\Collection
     */
    public function getAll($currentPage = null)
    {

        $query = $this->getModel();

        return $this->paginater($query, $currentPage);
    }

    public function getById(int $id)
    {

        $model = $this->getModel()->findOrFail($id);

        return $model;
    }

    public function delete(int $id)
    {

        $model = $this->getModel()->findOrFail($id);

        return $model->delete();
    }

    public function toggle(int $id, String $name)
    {
        $model = $this->getModel()->findOrFail($id);
        $attributes = $model->getAttributes();

        if (!isset($attributes[$name])) {
            throw new \Exception($name . ' Doesn\'t Exist', 3007);
        }

        if (
            !($model->{$name} === 0
                || $model->{$name} === 1
                || $model->{$name} === true
                || $model->{$name} === false)
        ) {
            throw new \Exception($name . ' is not boolean', 3024);
        }

        return $this->update($id, [$name => !($model->{$name})]);
    }

    /**
     * Get the value of page_limit
     */
    public function getPageLimit()
    {
        return $this->page_limit;
    }

    /**
     * Set the value of page_limit
     *
     * @return  self
     */
    public function setPageLimit($page_limit)
    {
        $this->page_limit = $page_limit;

        return $this;
    }
}
