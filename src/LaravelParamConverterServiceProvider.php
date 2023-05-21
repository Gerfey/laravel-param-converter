<?php

declare(strict_types=1);

namespace Gerfey\LaravelParamConverter;

use Gerfey\LaravelParamConverter\Middleware\ParamConverter;
use Illuminate\Support\ServiceProvider;

class LaravelParamConverterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../config/param-converter.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('param-converter.php');
        } else {
            $publishPath = base_path('config/param-converter.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');

        /** @var Router $router */
        $router = $this->app['router'];
        foreach ($router->getMiddlewareGroups() as $group => $middleware) {
            $router->pushMiddlewareToGroup($group, ParamConverter::class);
        }
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/param-converter.php';
        $this->mergeConfigFrom($configPath, 'param-converter');
    }
}
