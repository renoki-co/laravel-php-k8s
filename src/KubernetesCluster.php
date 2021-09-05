<?php

namespace RenokiCo\LaravelK8s;

use RenokiCo\PhpK8s\KubernetesCluster as PhpK8sCluster;

/**
 * @see \RenokiCo\PhpK8s\KubernetesCluster
 */
class KubernetesCluster
{
    /**
     * The Kubernetes cluster instance.
     *
     * @var \RenokiCo\PhpK8s\KubernetesCluster
     */
    protected $cluster;

    /**
     * Create a new Kubernetes Cluster.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config)
    {
        $this->loadFromConfig($config);
    }

    /**
     * Switch the connection.
     *
     * @param  string  $connection
     * @return \RenokiCo\LaravelK8s\KubernetesCluster
     */
    public function connection(string $connection)
    {
        $this->loadFromConfig(
            config('k8s.connections')[$connection] ?? config('k8s.default')
        );

        return $this;
    }

    /**
     * Load the Cluster instance from the given config.
     *
     * @param  array  $config
     * @return void
     */
    protected function loadFromConfig(array $config)
    {
        switch ($config['driver'] ?? null) {
            case 'kubeconfig': $this->configureWithKubeConfigFile($config); break;
            case 'http': $this->configureWithHttpAuth($config); break;
            case 'token': $this->configureWithToken($config); break;
            case 'cluster': $this->configureInCluster($config); break;
            case 'variable': $this->configureWithKubeConfigVariable($config); break;
            default: break;
        }
    }

    /**
     * Configure the cluster using a Kube Config file.
     *
     * @param  array  $config
     * @return void
     */
    protected function configureWithKubeConfigFile(array $config)
    {
        $this->cluster = PhpK8sCluster::fromKubeConfigYamlFile(
            $config['path'], $config['context']
        );
    }

    /**
     * Configure the cluster with HTTP authentication.
     *
     * @param  array  $config
     * @return void
     */
    protected function configureWithHttpAuth(array $config)
    {
        $this->cluster = PhpK8sCluster::fromUrl($config['host']);

        if ($config['ssl']['verify'] ?? true) {
            $this->cluster->withCertificate(
                $config['ssl']['certificate'] ?? null
            );

            $this->cluster->withPrivateKey(
                $config['ssl']['key'] ?? null
            );

            $this->cluster->withCaCertificate(
                $config['ssl']['ca'] ?? null
            );
        } else {
            $this->cluster->withoutSslChecks();
        }

        $this->cluster->httpAuthentication(
            $config['auth']['username'] ?? null,
            $config['auth']['password'] ?? null
        );
    }

    /**
     * Configure the cluster with a Bearer Token.
     *
     * @param  array $config
     * @return void
     */
    protected function configureWithToken(array $config)
    {
        $this->cluster = PhpK8sCluster::fromUrl($config['host']);

        if ($config['ssl']['verify'] ?? true) {
            $this->cluster->withCertificate(
                $config['ssl']['certificate'] ?? null
            );

            $this->cluster->withPrivateKey(
                $config['ssl']['key'] ?? null
            );

            $this->cluster->withCaCertificate(
                $config['ssl']['ca'] ?? null
            );
        } else {
            $this->cluster->withoutSslChecks();
        }

        $this->cluster->withToken($config['token']);
    }

    /**
     * Load the In-Cluster configuration.
     *
     * @param  array  $config
     * @return void
     */
    protected function configureInCluster(array $config)
    {
        $this->cluster = PhpK8sCluster::inClusterConfiguration();
    }

    /**
     * Configure the cluster using the
     * KUBECONFIG environment variable.
     *
     * @param  array  $config
     * @return void
     */
    protected function configureWithKubeConfigVariable(array $config)
    {
        $this->cluster = PhpK8sCluster::fromKubeConfigVariable($config['context']);
    }

    /**
     * Get the initialized cluster.
     *
     * @return \RenokiCo\PhpK8s\KubernetesCluster
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * Proxy the calls onto the cluster.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->getCluster()->{$method}(...$parameters);
    }

    /**
     * Proxy the static calls onto the cluster.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return PhpK8sCluster::{$method}(...$parameters);
    }
}
