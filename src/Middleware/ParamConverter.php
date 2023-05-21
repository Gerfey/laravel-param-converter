<?php

declare(strict_types=1);

namespace Gerfey\LaravelParamConverter\Middleware;

use Closure;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionType;
use ReflectionUnionType;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter as ConfigurationParamConverter;

class ParamConverter
{
    private array $config;
    private Container $container;
    private ParamConverterManager $manager;
    private bool $autoConvert;

    public function __construct(Container $container, ParamConverterManager $manager) {
        $this->container = $container;
        $this->manager = $manager;

        /** @var Repository $config */
        $config = $this->container->make(Repository::class);

        if ($config->has('param-converter')) {
            $this->config = $config->get('param-converter');
        }

        $this->autoConvert = $this->config['request']['converters'] ?? true;

        foreach ($this->getParamConverters() as $paramConverter) {
            $this->manager->add($paramConverter);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $controller = Str::parseCallback($route->getAction()['uses']);

        if (count($controller) < 2) {
            return $next($request);
        }

        $configurations = [];

        if ($this->autoConvert) {
            if (is_array($controller)) {
                $reflection = new ReflectionMethod($controller[0], $controller[1]);
            } elseif (is_object($controller) && is_callable([$controller, '__invoke'])) {
                $reflection = new ReflectionMethod($controller, '__invoke');
            } else {
                $reflection = new ReflectionFunction($controller);
            }

            $configurations = $this->autoConfigure($reflection, $request, $configurations);
        }

        $this->manager->apply($request, $configurations);

        foreach ($request->attributes->all() as $name => $class) {
            $route->setParameter($name, $class);
        }

        return $next($request);
    }

    private function autoConfigure(ReflectionFunctionAbstract $r, Request $request, $configurations)
    {
        foreach ($r->getParameters() as $param) {
            $type = $param->getType();
            $class = $this->getParamClassByType($type);
            if (null !== $class && $request instanceof $class) {
                continue;
            }

            $name = $param->getName();

            if ($type) {
                if (!isset($configurations[$name])) {
                    $configuration = new ConfigurationParamConverter([]);
                    $configuration->setName($name);

                    $configurations[$name] = $configuration;
                }

                if (null !== $class && null === $configurations[$name]->getClass()) {
                    $configurations[$name]->setClass($class);
                }
            }

            if (isset($configurations[$name])) {
                $configurations[$name]->setIsOptional($param->isOptional() || $param->isDefaultValueAvailable() || ($type && $type->allowsNull()));
            }
        }

        return $configurations;
    }

    private function getParamClassByType(?ReflectionType $type): ?string
    {
        if (null === $type) {
            return null;
        }

        foreach ($type instanceof ReflectionUnionType ? $type->getTypes() : [$type] as $type) {
            if (!$type->isBuiltin()) {
                return $type->getName();
            }
        }

        return null;
    }

    protected function getParamConverters(): array
    {
        /** @var ParamConverterInterface[] $converters */
        return array_map(function ($converter) {
            return $this->container->make($converter);
        }, $this->config['converters'] ?? []);
    }
}
