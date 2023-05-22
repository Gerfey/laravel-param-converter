## Оглавление

- [Основная информация / Basic information](../../README.md)
- *Примеры / Examples*
    - [Токен / Token](token.md)

## Токен / Tokens

Представим ситуацию, нам понадобилось прокидывать ```X-Auth-Token``` из заголовков в контроллер, для дальнейшей работы с ним.

### Контроллер
Контроллер может иметь такой вид:

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

class TestController extends Controller
{
    public function index(string $token): JsonResponse
    {
        return new JsonResponse(['token' => $token]);
    }
}
```
Если вы попробуете выполнить код, то получите ошибку, сообщающая нам о том,
что мы передаем слишком мало аргументов в функцию ```App\\Http\\Controllers\\TestController::index()```

Чтобы убрать данную ошибку и решить нашу задачу создадим свой ParamConverter, чтобы извлекать токен из заголовка. 

### Custom ParamConverter

Все ParamConverters должны реализовывать [ParamConverterInterface](https://symfony.com/bundles/SensioFrameworkExtraBundle/current/annotations/converters.html#creating-a-converter).

Создадим свой:

```php
<?php

declare(strict_types=1);

namespace App\Components\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return true;
    }
}
```

1. Метод ```supports(ParamConverter $configuration): bool;```
проверяет, что ParamConveter может быть применен с данными, предоставленными в $configuration.
Если все в порядке, результатом будет true. В противном случае будет false и ParamConverter перемещается в другой конвертер.
2. Метод ```apply(Request $request, ParamConverter $configuration): bool;``` выполняется при успешном варианте supports и тут мы выполняем нашу логику (преобразование). 

Добавим проверку в supports:

```php
    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() === 'token';
    }
```

Если TestController::index(), будет найден атрибут с наименованием token, то мы начнем выполнять метод TokenParamConverter::apply().

Добавим логику в apply:

```php
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $request->attributes->set(
            $configuration->getName(),
            $request->headers->get('X-Auth-Token')
        );

        return true;
    }
```
Во время выполнения наш ParamConverter собирает и преобразует все поддерживаемые атрибуты из переменной attributes запроса $request->attributes->all()

Поэтому мы получаем значение токена из заголовка X-Auth-Token и присваиваем переменной attributes с помощью метода ParameterBag.

Итоговый вид нашего TokenParamConverter:

```php
<?php

declare(strict_types=1);

namespace App\Components\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $request->attributes->set(
            $configuration->getName(),
            $request->headers->get('X-Auth-Token')
        );

        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        return $configuration->getName() === 'token';
    }
}
```

Наш пользовательский TokenParamConverter завершен. Теперь мы можем им пользоваться.
Осталось только добавить его в файле конфигурации config/param-converter.php в ключ с перечислением converters. 

```php
<?php

use App\Components\Request\ParamConverter\TokenParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DateTimeParamConverter;

return [
    'request' => [
        'converters' => true,
    ],
    'converters' => [
        DateTimeParamConverter::class,
        TokenParamConverter::class
    ]
];
```

При передаче заголовка X-Auth-Token со значением fa8426a0-8eaf-4d22-8e13-7c1b16a9370c на наш TestController::index() возвращается следующая структура:

```json
{
    "token": "fa8426a0-8eaf-4d22-8e13-7c1b16a9370c"
}
```

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