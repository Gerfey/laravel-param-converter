<?php

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DateTimeParamConverter;

return [
    'request' => [
        'converters' => true,
    ],
    'converters' => [
        DateTimeParamConverter::class,
    ]
];
