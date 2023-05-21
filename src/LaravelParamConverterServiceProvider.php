<?php

declare(strict_types=1);

namespace Gerfey\LaravelParamConverter;

use Gerfey\LaravelParamConverter\Middleware\ParamConverter;
use Illuminate\Support\ServiceProvider;

class LaravelParamConverterServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'param-converter');
    }

    public function boot()
    {
        if (function_exists('config_path')) {
            $publishPath = config_path('param-converter.php');
        } else {
            $publishPath = base_path('config/param-converter.php');
        }

        $this->publishes([$this->configPath() => $publishPath], 'param-converter');

        /** @var Router $router */
        $router = $this->app['router'];
        foreach ($router->getMiddlewareGroups() as $group => $middleware) {
            $router->pushMiddlewareToGroup($group, ParamConverter::class);
        }
    }

    private function configPath()
    {
        return __DIR__ . '/../config/param-converter.php';
    }
}
