Laravel PHP K8s
===============

![CI](https://github.com/renoki-co/laravel-php-k8s/workflows/CI/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/renoki-co/laravel-php-k8s/branch/master/graph/badge.svg)](https://codecov.io/gh/renoki-co/laravel-php-k8s/branch/master)
[![StyleCI](https://github.styleci.io/repos/307696878/shield?branch=master)](https://github.styleci.io/repos/307696878)
[![Latest Stable Version](https://poser.pugx.org/renoki-co/laravel-php-k8s/v/stable)](https://packagist.org/packages/renoki-co/laravel-php-k8s)
[![Total Downloads](https://poser.pugx.org/renoki-co/laravel-php-k8s/downloads)](https://packagist.org/packages/renoki-co/laravel-php-k8s)
[![Monthly Downloads](https://poser.pugx.org/renoki-co/laravel-php-k8s/d/monthly)](https://packagist.org/packages/renoki-co/laravel-php-k8s)
[![License](https://poser.pugx.org/renoki-co/laravel-php-k8s/license)](https://packagist.org/packages/renoki-co/laravel-php-k8s)

Just a simple port of [renoki-co/php-k8s](https://github.com/renoki-co/php-k8s) for easier access in Laravel.

## 🤝 Supporting

Renoki Co. on GitHub aims on bringing a lot of open source projects and helpful projects to the world. Developing and maintaining projects everyday is a harsh work and tho, we love it.

If you are using your application in your day-to-day job, on presentation demos, hobby projects or even school projects, spread some kind words about our work or sponsor our work. Kind words will touch our chakras and vibe, while the sponsorships will keep the open source projects alive.

[![ko-fi](https://www.ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/R6R42U8CL)

## 🚀 Installation

You can install the package via composer:

```bash
composer require renoki-co/laravel-php-k8s
```

Publish the config:

```bash
$ php artisan vendor:publish --provider="RenokiCo\LaravelK8s\LaravelK8sServiceProvider" --tag="config"
```

## 🙌 Usage

The cluster configuration can be found in the `config/k8s.php` file. You can get started directly with the `/.kube/config` file you already have.

```php
use RenokiCo\LaravelK8s\LaravelK8sFacade;

foreach (LaravelK8sFacade::getAllConfigMaps() as $cm) {
    // $cm->getName();
}
```

For further documentation, check [renoki-co/php-k8s](https://github.com/renoki-co/php-k8s).

## Multiple connections

The package supports multiple connections configurations. If you wish to select a specific one (not the default one), call `connection` before getting the cluster.

```php
use RenokiCo\LaravelK8s\LaravelK8sFacade;

$cluster = LaravelK8sFacade::connection('http')->getCluster();
```

## Getting the cluster instance

You can also call `getCluster()` to get the instance of `\RenokiCo\PhpK8s\KubernetesCluster`:

```php
$cluster = LaravelK8sFacade::getCluster();
```

## 🐛 Testing

``` bash
vendor/bin/phpunit
```

## 🤝 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## 🔒  Security

If you discover any security related issues, please email alex@renoki.org instead of using the issue tracker.

## 🎉 Credits

- [Alex Renoki](https://github.com/rennokki)
- [All Contributors](../../contributors)
