<?php

namespace RenokiCo\LaravelK8s\Test;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($_SERVER['KUBECONFIG']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPackageProviders($app)
    {
        return [
            \RenokiCo\LaravelK8s\LaravelK8sServiceProvider::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'wslxrEFGWY6GfGhvN9L3wH3KSRJQQpBD');
    }
}
