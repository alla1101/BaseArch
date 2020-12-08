<?php

namespace BaseTools\Transformers;

use League\Fractal\TransformerAbstract;

class NoTransformer extends TransformerAbstract
{

    protected $availableIncludes = [];

    public function transform($result)
    {
        return $result->toArray();
    }
}
