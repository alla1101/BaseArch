<?php

namespace Alla\BaseArch\Tests;

use Alla\BaseArch\BaseArchServiceProvider;
use Orchestra\Testbench\TestCase as TC;

class TestCase extends TC
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            BaseArchServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'basearch',
            'username' => 'root',
            'password' => 'secret'
        ]);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {

        // Custom Code

        // Original Code
        parent::tearDown();
    }
    /**
     * Set up the database.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function setUpDatabase($app)
    {
    }
}
