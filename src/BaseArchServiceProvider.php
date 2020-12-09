<?php

namespace Alla\BaseArch;


use Illuminate\Support\ServiceProvider;

class BaseArchServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
        }
    }
    public function register()
    {
        //
    }
}
