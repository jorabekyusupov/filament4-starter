<?php

return [

    'label' => 'Саҳифалар навигацияси',

    'overview' => '{1} 1 натижа кўрсатилмоқда |[2,*] :firstдан :lastгача жами натижалар :totalта',

    'fields' => [

        'records_per_page' => [

            'label' => 'Ҳар бир саҳифага',

            'options' => [
                'all' => 'Барчаси',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => ':pageчи саҳифага ўтиш',
        ],

        'next' => [
            'label' => 'Кейинги',
        ],

        'previous' => [
            'label' => 'Олдинги',
        ],

    ],

];
