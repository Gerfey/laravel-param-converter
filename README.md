# Laravel Param Converter

[![Software License][badge-license]][license]
[![Total Downloads][badge-downloads]][downloads]

Добавляем возможность работать c Symfony ParamConverter в Laravel.

## Установка

Предпочтительный способ установки - через [Packagist][] и [Composer][]. Запустите
следующую команду, чтобы установить пакет и добавить его в качестве require в
"composer.json" вашего проекта:

```bash
composer require gerfey/laravel-param-converter
```

## Настройка

Вам нужно опубликовать конфигурацию, чтобы добавлять свои собственные преобразователи параметров. Конфигурация доступна по пути ```config/param-converter.php```

```bash
php artisan vendor:publish --tag="param-converter"
```

## Собственные преобразователи параметров 

Изучаем [документацию](http://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html#creating-a-converter) по преобразователям.

Все преобразователи должны реализовывать ```ParamConverterInterface```.

Готовый преобразователь параметров, нужно будет подключить в файле конфигурации ```config/param-converter.php``` ключ с перечислением ```converters```.

## Copyright and License

The gerfey/laravel-param-converter library is copyright © [Alexander Levchenkov](https://vk.com/gerfey) and
licensed for use under the MIT License (MIT). Please see [LICENSE][] for more
information.

[packagist]: https://packagist.org/packages/gerfey/laravel-param-converter
[composer]: http://getcomposer.org/

[badge-source]: https://img.shields.io/badge/source-gerfey/laravel-param-converter-blue.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-build]: https://img.shields.io/travis/gerfey/laravel-param-converter/master.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/gerfey/laravel-param-converter.svg?style=flat-square

[source]: https://github.com/Gerfey/laravel-param-converter
[release]: https://packagist.org/packages/Gerfey/laravel-param-converter
[license]: https://github.com/Gerfey/laravel-param-converter/blob/master/LICENSE
[build]: https://travis-ci.org/Gerfey/laravel-param-converter
[downloads]: https://packagist.org/packages/Gerfey/laravel-param-converter