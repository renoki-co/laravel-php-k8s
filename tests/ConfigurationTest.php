<?php

namespace RenokiCo\LaravelK8s\Test;

use RenokiCo\LaravelK8s\LaravelK8sFacade;
use RenokiCo\PhpK8s\Kinds\K8sResource;

class ConfigurationTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_kube_config_file()
    {
        $this->app['config']->set('k8s.default', 'kubeconfig');
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
        $this->app['config']->set('k8s.default', 'http');
        $this->app['config']->set('k8s.connections.http', [
            'driver' => 'http',
            'host' => env('KUBE_HOST', 'http://127.0.0.1:8080'),
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
        $this->app['config']->set('k8s.default', 'token');
        $this->app['config']->set('k8s.connections.token', [
            'driver' => 'token',
            'host' => env('KUBE_HOST', 'http://127.0.0.1:8080'),
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
        $this->app['config']->set('k8s.default', 'cluster');
        $this->app['config']->set('k8s.connections.cluster', [
            'driver' => 'cluster',
            'host' => env('KUBE_HOST', 'https://kubernetes.default.svc.cluster.local'),
        ]);

        $cluster = LaravelK8sFacade::connection('cluster')->getCluster();

        [
            'headers' => ['authorization' => $token],
            'verify' => $caPath,
        ] = $cluster->getClient()->getConfig();

        $this->assertEquals('Bearer some-token', $token);
        $this->assertEquals('/var/run/secrets/kubernetes.io/serviceaccount/ca.crt', $caPath);
        $this->assertEquals('some-namespace', K8sResource::$defaultNamespace);
    }

    /**
     * @dataProvider environmentVariableContextProvider
     */
    public function test_from_environment_variable(string $context = null, string $expectedDomain)
    {
        $_SERVER['KUBECONFIG'] = __DIR__.'/cluster/kubeconfig.yaml::'.__DIR__.'/cluster/kubeconfig-2.yaml';

        $this->app['config']->set('k8s.default', 'variable');
        $this->app['config']->set('k8s.connections.variable', [
            'driver' => 'variable',
            'context' => $context,
        ]);

        $cluster = LaravelK8sFacade::connection('variable')->getCluster();

        $this->assertSame("https://{$expectedDomain}:8443/?", $cluster->getCallableUrl('/', []));
    }

    public function environmentVariableContextProvider(): iterable
    {
        yield [null, 'minikube'];
        yield ['minikube-2', 'minikube-2'];
        yield ['minikube-3', 'minikube-3'];
    }
}
