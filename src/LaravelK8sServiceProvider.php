<?php

namespace RenokiCo\LaravelK8s;

use Illuminate\Support\ServiceProvider;

class LaravelK8sServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/k8s.php' => config_path('k8s.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../config/k8s.php', 'k8s'
        );

        $this->app->bind('laravel.k8s', function ($app) {
            $config = $app['config']['k8s'];
            $connection = $config['default'] ?? 'kubeconfig';

            return new KubernetesCluster(
                $config['connections'][$connection] ?? []
            );
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
