<?php

namespace RenokiCo\LaravelK8s\Test;

use RenokiCo\LaravelK8s\LaravelK8sFacade;
use RenokiCo\PhpK8s\Kinds\K8sResource;

class ConfigurationTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_kube_config_file()
    {
        $this->app['config']->set('k8s.connections.kubeconfig', [
            'driver' => 'kubeconfig',
            'path' => __DIR__.'/cluster/kubeconfig.yaml',
            'context' => 'minikube-2',
        ]);

        LaravelK8sFacade::setTempFolder(__DIR__.DIRECTORY_SEPARATOR.'temp');

        $cluster = LaravelK8sFacade::connection('kubeconfig')->getCluster();

        [
            'verify' => $caPath,
            'cert' => $certPath,
            'ssl_key' => $keyPath,
        ] = $cluster->getClient()->getConfig();

        $this->assertEquals('/path/to/.minikube/ca.crt', $caPath);
        $this->assertEquals('/path/to/.minikube/client.crt', $certPath);
        $this->assertEquals('/path/to/.minikube/client.key', $keyPath);
    }

    public function test_http_authentication()
    {
        $this->app['config']->set('k8s.connections.http', [
            'driver' => 'http',
            'host' => env('KUBE_HOST', '127.0.0.1'),
            'port' => env('KUBE_PORT', 8080),
            'ssl' => [
                'certificate' => '/path/to/.minikube/client.crt',
                'key' => '/path/to/.minikube/client.key',
                'ca' => '/path/to/.minikube/ca.crt',
                'verify' => true,
            ],
            'auth' => [
                'username' => 'some-user',
                'password' => 'some-password',
            ],
        ]);

        LaravelK8sFacade::setTempFolder(__DIR__.DIRECTORY_SEPARATOR.'temp');

        $cluster = LaravelK8sFacade::connection('http')->getCluster();

        [
            'verify' => $caPath,
            'cert' => $certPath,
            'ssl_key' => $keyPath,
            'auth' => $auth,
        ] = $cluster->getClient()->getConfig();

        $this->assertEquals('/path/to/.minikube/ca.crt', $caPath);
        $this->assertEquals('/path/to/.minikube/client.crt', $certPath);
        $this->assertEquals('/path/to/.minikube/client.key', $keyPath);

        $this->assertEquals('some-user', $auth[0]);
        $this->assertEquals('some-password', $auth[1]);
    }

    public function test_token_authentication()
    {
        $this->app['config']->set('k8s.connections.token', [
            'driver' => 'token',
            'host' => env('KUBE_HOST', '127.0.0.1'),
            'port' => env('KUBE_PORT', 8080),
            'ssl' => [
                'certificate' => '/path/to/.minikube/client.crt',
                'key' => '/path/to/.minikube/client.key',
                'ca' => '/path/to/.minikube/ca.crt',
                'verify' => true,
            ],
            'token' => 'some-token',
        ]);

        LaravelK8sFacade::setTempFolder(__DIR__.DIRECTORY_SEPARATOR.'temp');

        $cluster = LaravelK8sFacade::connection('token')->getCluster();

        [
            'verify' => $caPath,
            'cert' => $certPath,
            'ssl_key' => $keyPath,
            'headers' => [
                'authorization' => $token,
            ],
        ] = $cluster->getClient()->getConfig();

        $this->assertEquals('/path/to/.minikube/ca.crt', $caPath);
        $this->assertEquals('/path/to/.minikube/client.crt', $certPath);
        $this->assertEquals('/path/to/.minikube/client.key', $keyPath);

        $this->assertEquals('Bearer some-token', $token);
    }

    public function test_in_cluster_config()
    {
        $cluster = LaravelK8sFacade::connection('cluster')->getCluster();

        [
            'headers' => ['authorization' => $token],
            'verify' => $caPath,
        ] = $cluster->getClient()->getConfig();

        $this->assertEquals('Bearer some-token', $token);
        $this->assertEquals('/var/run/secrets/kubernetes.io/serviceaccount/ca.crt', $caPath);
        $this->assertEquals('some-namespace', K8sResource::$defaultNamespace);
    }
}
