<?php

namespace BaseTools\Controllers;

use BaseTools\ComplexSearch\ComplexSearchControllerTrait;

trait BaseCodeForController
{

    public function HandlePage($page)
    {
        $currentPage = (is_numeric($page) || $page === "InternalAPICall") ? $page : 1;

        return $currentPage;
    }
}
