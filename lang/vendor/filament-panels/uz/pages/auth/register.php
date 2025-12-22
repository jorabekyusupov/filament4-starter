<?php

return [

    'title' => 'Рўйхатдан ўтиш',

    'heading' => 'Ҳисоб қайдномасини рўйхатдан ўтказинг',

    'actions' => [

        'login' => [
            'before' => 'ёки',
            'label' => 'ўз ҳисоб қайдномангиз орқали киринг',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Электрон почта манзили',
        ],

        'name' => [
            'label' => 'Исм',
        ],

        'password' => [
            'label' => 'Парол',
            'validation_attribute' => 'парол',
        ],

        'password_confirmation' => [
            'label' => 'Паролни тасдиқлаш',
        ],

        'actions' => [

            'register' => [
                'label' => 'Рўйхатдан ўтиш',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Рўйхатдан ўтишга уринишлар жуда кўп',
            'body' => 'Илтимос, :seconds сониядан кейин қайта уриниб кўринг.',
        ],

    ],


];
