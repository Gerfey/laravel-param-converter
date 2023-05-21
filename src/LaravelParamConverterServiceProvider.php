<?php

namespace Gerfey\LaravelParamConverter;

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
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/param-converter.php';
        $this->mergeConfigFrom($configPath, 'param-converter');
    }
}
