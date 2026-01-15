<?php

return [

    'column_toggle' => [

        'heading' => 'Устунлар',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => ':countтадан камроқ кўрсатиш',
                'expand_list' => ':countтадан кўпроқ кўрсатиш',
            ],

            'more_list_items' => 'ва :countтадан кўп',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Оммавий амаллар учун танлаш/бекор қилиш.',
        ],

        'bulk_select_record' => [
            'label' => ':label elementi оммавий амаллар учун танлаш/бекор қилиш.',
        ],

        'bulk_select_group' => [
            'label' => 'Сарлавҳа гуруҳи учун танлаш/бекор қилиш.',
        ],

        'search' => [
            'label' => 'Қидириш',
            'placeholder' => 'Қидириш',
            'indicator' => 'Қидириш',
        ],

    ],

    'summary' => [

        'heading' => 'Хулоса',

        'subheadings' => [
            'all' => 'Барча :labelлар',
            'group' => ':group хулоса',
            'page' => 'Ушбу саҳифа',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Ўртача',
            ],

            'count' => [
                'label' => 'Ҳисоблаш',
            ],

            'sum' => [
                'label' => 'Жами',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Ёзувларни қайта тартиблашни тугатиш',
        ],

        'enable_reordering' => [
            'label' => 'Ёзувларни қайта тартиблаш',
        ],

        'filter' => [
            'label' => 'Филтрлаш',
        ],

        'group' => [
            'label' => 'Гуруҳлаш',
        ],

        'open_bulk_actions' => [
            'label' => 'Оммавий амаллар',
        ],

        'toggle_columns' => [
            'label' => 'Устунларни ўтиш',
        ],

    ],

    'empty' => [

        'heading' => ':model мавжуд эмас',

        'description' => 'Бошлаш учун :model яратинг',

    ],

    'filters' => [

        'actions' => [
            'apply' => [
                'label' => 'Филтрларни қўллаш',
            ],
            'remove' => [
                'label' => 'Филтрларни олиб ташлаш',
            ],

            'remove_all' => [
                'label' => 'Барча филтрларни олиб ташлаш',
                'tooltip' => 'Барча филтрларни олиб ташлаш',
            ],

            'reset' => [
                'label' => 'Филтрни тозалаш',
            ],

        ],

        'heading' => 'Филтрлар',

        'indicator' => 'Фаол филтрлар',

        'multi_select' => [
            'placeholder' => 'Барчаси',
        ],

        'select' => [
            'placeholder' => 'Барчаси',
        ],

        'trashed' => [

            'label' => 'Ўчирилган маълумотлар',

            'only_trashed' => 'Фақат ўчирилган маълумотлар',

            'with_trashed' => 'Ўчирилган ва ўчирилмаган маълумотлар',

            'without_trashed' => 'Ўчирилмаган маълумотлар',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Гуруҳлаш',
                'placeholder' => 'Гуруҳлаш',
            ],

            'direction' => [

                'label' => 'Гуруҳ йўналиши',

                'options' => [
                    'asc' => 'Кўтариш (ASC)',
                    'desc' => 'Тўшиш (DESC)',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Тартиблаш',

    'selection_indicator' => [

        'selected_count' => '1та маълумот танланган|:countта маълумотлар танланган',

        'actions' => [

            'select_all' => [
                'label' => ':count - Барчасини танлаш',
            ],

            'deselect_all' => [
                'label' => 'Барчасини бекор қилиш',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Саралаш',
            ],

            'direction' => [

                'label' => 'Саралаш',

                'options' => [
                    'asc' => 'Кўтариш (ASC)',
                    'desc' => 'Тўшиш (DESC)',
                ],

            ],

        ],

    ],

];
