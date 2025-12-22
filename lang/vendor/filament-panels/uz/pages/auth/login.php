<?php

return [

    'title' => 'Тизимга кириш',

    'heading' => 'Ҳисобингизга киринг',

    'actions' => [

        'register' => [
            'before' => 'ёки',
            'label' => 'ҳисоб қайдномасини рўйхатдан ўтказинг',
        ],

        'request_password_reset' => [
            'label' => 'Паролни унутдингизми?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Электрон почта манзили',
        ],

        'password' => [
            'label' => 'Парол',
        ],

        'remember' => [
            'label' => 'Мени эслаб қол',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Ҳисоб қайдномасига кириш',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Сиз киритган фойдаланувчи номи ёки парол нотўғри.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Кириш учун уринишлар сони жуда кўп',
            'body' => 'Илтимос, :seconds сониядан кейин қайта уриниб кўринг.',
        ],

    ],

];
