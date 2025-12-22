<?php

return [

    'title' => 'Паролни қайта ўрнатиш',

    'heading' => 'Паролни қайта ўрнатиш',

    'form' => [

        'email' => [
            'label' => 'Электрон почта манзили',
        ],

        'password' => [
            'label' => 'Парол',
            'validation_attribute' => 'парол',
        ],

        'password_confirmation' => [
            'label' => 'Паролни тасдиқлаш',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Паролни қайта ўрнатиш',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Уринишлар жуда кўп',
            'body' => 'Илтимос, :seconds сониядан сўнг қайта уриниб кўринг.',
        ],

    ],


];
