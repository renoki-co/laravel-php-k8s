<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | The default connection that PHP K8s will connect to.
    |
    */

    'default' => env('KUBE_CONNECTION', null),

    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | Kubeconfig Driver
        |--------------------------------------------------------------------------
        |
        | Kubeconfig Driver works by reading from the KubeConfig file
        | and then selecting a specific context to work with.
        | Please see the documentation on contexts:
        | https://kubernetes.io/docs/tasks/access-application-cluster/configure-access-multiple-clusters/
        |
        */

        'kubeconfig' => [
            'driver' => 'kubeconfig',
            'path' => env('KUBECONFIG_PATH', '/.kube/config'),
            'context' => env('KUBECONFIG_CONTEXT', 'minikube'),
        ],

        /*
        |--------------------------------------------------------------------------
        | HTTP Driver
        |--------------------------------------------------------------------------
        |
        | Proxy Driver works by directly making a request to the API URL using
        | the HTTP authentication through username and password.
        |
        */

        'http' => [
            'driver' => 'http',
            'host' => env('KUBE_HOST', 'http://127.0.0.1:8080'),
            'ssl' => [
                'certificate' => env('KUBE_CERTIFICATE_PATH', null),
                'key' => env('KUBE_KEY_PATH', null),
                'ca' => env('KUBE_CA_PATH', null),
                'verify' => env('KUBE_VERIFY_SSL', true),
            ],
            'auth' => [
                'username' => env('KUBE_HTTP_USER', null),
                'password' => env('KUBE_HTTP_PASSWORD', null),
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Bearer Token Driver
        |--------------------------------------------------------------------------
        |
        | Proxy Driver works by directly making a request to the API URL using
        | the bearer token authentication.
        |
        */

        'token' => [
            'driver' => 'token',
            'host' => env('KUBE_HOST', 'http://127.0.0.1:8080'),
            'ssl' => [
                'certificate' => env('KUBE_CERTIFICATE_PATH', null),
                'key' => env('KUBE_KEY_PATH', null),
                'ca' => env('KUBE_CA_PATH', null),
                'verify' => env('KUBE_VERIFY_SSL', true),
            ],
            'token' => env('KUBE_BEARER_TOKEN', null),
        ],

        /*
        |--------------------------------------------------------------------------
        | In-Cluster Driver
        |--------------------------------------------------------------------------
        |
        | In-Cluster Driver works only if the written PHP app runs
        | inside a Kubernetes Pod, within a Cluster. The configuration
        | is being loaded automatically.
        |
        */

        'cluster' => [
            'driver' => 'cluster',
            'host' => env('KUBE_HOST', 'https://kubernetes.default.svc.cluster.local'),
        ],

        /*
        |--------------------------------------------------------------------------
        | Environment Variable Driver
        |--------------------------------------------------------------------------
        |
        | The environment variable driver leverages your current (possibly set)
        | KUBECONFIG environment variable. The variable contains a list of paths
        | towards multiple kubeconfig files that will be read, merged and based
        | on the selected context from the configuration, it will connect
        | to the cluster, just like the "kubeconfig" driver.
        |
        | Read more: https://kubernetes.io/docs/tasks/access-application-cluster/configure-access-multiple-clusters/
        |
        */

        'environment' => [
            'driver' => 'environment',
            'context' => env('KUBECONFIG_CONTEXT', 'minikube'),
        ],

    ],

];
