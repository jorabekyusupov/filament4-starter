<?php

return [
    'label' => 'Сўров яратувчи',

    'form' => [
        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [
            'label' => 'Гуруҳлар',

            'block' => [
                'label' => 'Ажралиш (ЁКИ)',
                'or' => 'ёки',
            ],
        ],

        'rules' => [
            'label' => 'Қоидалар',

            'item' => [
                'and' => 'ВА',
            ],
        ],
    ],

    'no_rules' => '(Қоидалар йўқ)',

    'item_separators' => [
        'and' => 'ВА',
        'or' => 'ЁКИ',
    ],

    'operators' => [
        'is_filled' => [
            'label' => [
                'direct' => 'Тўлдирилган бўлса',
                'inverse' => 'Бўш бўлса',
            ],

            'summary' => [
                'direct' => ':attribute тўлган бўлса',
                'inverse' => ':attribute бўш бўлса',
            ],
        ],

        'boolean' => [
            'is_true' => [
                'label' => [
                    'direct' => 'Рост бўлса',
                    'inverse' => 'Ёлғон бўлса',
                ],

                'summary' => [
                    'direct' => ':attribute рост бўлса',
                    'inverse' => ':attribute ёлғон бўлса',
                ],
            ],
        ],

        'date' => [
            'is_after' => [
                'label' => [
                    'direct' => 'Кейин',
                    'inverse' => 'Кейин эмас',
                ],

                'summary' => [
                    'direct' => ':date дан кейин :attribute бўлса',
                    'inverse' => ':date :attribute дан кейин бўлмаса',
                ],
            ],

            'is_before' => [
                'label' => [
                    'direct' => 'Олдин бўлса',
                    'inverse' => 'Олдин бўлмаса',
                ],

                'summary' => [
                    'direct' => ':date дан олдин :attribute бўлса',
                    'inverse' => ':date :attribute дан олдин бўлмаса',
                ],
            ],

            'is_date' => [
                'label' => [
                    'direct' => 'Сана бўлса',
                    'inverse' => 'Сана бўлмаса',
                ],

                'summary' => [
                    'direct' => ':attribute :date бўлса',
                    'inverse' => ':attribute :date бўлмаса',
                ],
            ],

            'is_month' => [
                'label' => [
                    'direct' => 'Ой бўлса',
                    'inverse' => 'Ой бўлмаса',
                ],

                'summary' => [
                    'direct' => ':attribute :month бўлса',
                    'inverse' => ':attribute :month бўлмаса',
                ],
            ],

            'is_year' => [
                'label' => [
                    'direct' => 'Йил бўлса',
                    'inverse' => 'Йил бўлмаса',
                ],

                'summary' => [
                    'direct' => ':attribute :year бўлса',
                    'inverse' => ':attribute :year бўлмаса',
                ],
            ],

            'form' => [
                'date' => [
                    'label' => 'Сана',
                ],

                'month' => [
                    'label' => 'Ой',
                ],

                'year' => [
                    'label' => 'Йил',
                ],
            ],
        ],

        'number' => [
            'equals' => [
                'label' => [
                    'direct' => 'Тенг',
                    'inverse' => 'Тенг эмас',
                ],

                'summary' => [
                    'direct' => ':attribute тенг :number га',
                    'inverse' => ':attribute тенг эмас :number га',
                ],
            ],

            'is_max' => [
                'label' => [
                    'direct' => 'Максимал бўлса',
                    'inverse' => 'Дан катта бўлса',
                ],

                'summary' => [
                    'direct' => ':attribute максимал :number',
                    'inverse' => ':attribute :number дан катта',
                ],
            ],

            'is_min' => [
                'label' => [
                    'direct' => 'Минимал',
                    'inverse' => 'Дан кичкина',
                ],

                'summary' => [
                    'direct' => ':attribute минимал :number',
                    'inverse' => ':attribute :number дан кичкина',
                ],
            ],

            'aggregates' => [
                'average' => [
                    'label' => 'Ўртача',
                    'summary' => 'Ўртача :attribute',
                ],

                'max' => [
                    'label' => 'Максимал',
                    'summary' => 'Максимал :attribute',
                ],

                'min' => [
                    'label' => 'Минимал',
                    'summary' => 'Минимал :attribute',
                ],

                'sum' => [
                    'label' => 'Умумий натижа (SUM)',
                    'summary' => 'Умумий натижа (SUM) :attribute',
                ],
            ],

            'form' => [
                'aggregate' => [
                    'label' => 'Агрегат',
                ],

                'number' => [
                    'label' => 'Рақам',
                ],
            ],
        ],

        'relationship' => [
            'equals' => [
                'label' => [
                    'direct' => 'Мавжуд',
                    'inverse' => 'Мавжуд эмас',
                ],

                'summary' => [
                    'direct' => ':relationship мавжуд :count та',
                    'inverse' => ':relationship мавжуд эмас :count',
                ],
            ],

            'has_max' => [
                'label' => [
                    'direct' => 'Максималга эга',
                    'inverse' => 'Дан ортиқ',
                ],

                'summary' => [
                    'direct' => ':relationship :count максималга эга',
                    'inverse' => ':relationship :count дан ортиқ',
                ],
            ],

            'has_min' => [
                'label' => [
                    'direct' => 'Минималга эга',
                    'inverse' => 'Дан кам',
                ],

                'summary' => [
                    'direct' => ':relationship :count минималга эга',
                    'inverse' => ':count :relationship дан ортиқ',
                ],
            ],

            'is_empty' => [
                'label' => [
                    'direct' => 'Бўш бўлса',
                    'inverse' => 'Бўш бўлмаса',
                ],

                'summary' => [
                    'direct' => ':relationship бўш',
                    'inverse' => ':relationship бўш эмас',
                ],
            ],

            'is_related_to' => [
                'label' => [
                    'single' => [
                        'direct' => 'Ҳисобланади',
                        'inverse' => 'Ҳисобланмайди',
                    ],

                    'multiple' => [
                        'direct' => 'Ўз ичига олади',
                        'inverse' => 'Ўз ичига олмайди',
                    ],
                ],

                'summary' => [
                    'single' => [
                        'direct' => ':values :relationship',
                        'inverse' => ':values :relationship эмас',
                    ],

                    'multiple' => [
                        'direct' => ':relationship :values ни ўз ичига олади',
                        'inverse' => ':relationship :values ни ўз ичига олмайди',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ёки ',
                    ],
                ],

                'form' => [
                    'value' => [
                        'label' => 'Қиймат',
                    ],

                    'values' => [
                        'label' => 'Қийматлар',
                    ],
                ],
            ],

            'form' => [
                'count' => [
                    'label' => 'Сони',
                ],
            ],
        ],

        'select' => [
            'is' => [
                'label' => [
                    'direct' => 'Ҳисобланади',
                    'inverse' => 'Ҳисобланмайди',
                ],

                'summary' => [
                    'direct' => ':attribute :values ҳисобланади',
                    'inverse' => ':attribute :values ҳисобланмайди',
                    'values_glue' => [
                        ', ',
                        'final' => ' ёки ',
                    ],
                ],

                'form' => [
                    'value' => [
                        'label' => 'Қиймат',
                    ],

                    'values' => [
                        'label' => 'Қийматлар',
                    ],
                ],
            ],
        ],

        'text' => [
            'contains' => [
                'label' => [
                    'direct' => 'Ўз ичига олади',
                    'inverse' => 'Ўз ичига олмаган',
                ],

                'summary' => [
                    'direct' => ':attribute :text ни ўз ичига олади',
                    'inverse' => ':attribute :text ўз ичига олмайди',
                ],
            ],

            'ends_with' => [
                'label' => [
                    'direct' => 'Билан тугайди',
                    'inverse' => 'Билан тугамайди',
                ],

                'summary' => [
                    'direct' => ':attribute :text билан тугайди',
                    'inverse' => ':attribute :text билан тугамайди',
                ],
            ],

            'equals' => [
                'label' => [
                    'direct' => 'Тенг',
                    'inverse' => 'Тенг эмас',
                ],

                'summary' => [
                    'direct' => ':attribute :text га тенг',
                    'inverse' => ':attribute :text га тенг эмас',
                ],
            ],

            'starts_with' => [
                'label' => [
                    'direct' => 'Билан бошланади',
                    'inverse' => 'Билан бошланмайди',
                ],

                'summary' => [
                    'direct' => ':attribute :text билан бошланади',
                    'inverse' => ':attribute :text билан бошланмайди',
                ],
            ],

            'form' => [
                'text' => [
                    'label' => 'Матн',
                ],
            ],
        ],
    ],

    'actions' => [
        'add_rule' => [
            'label' => 'Қоида қўшиш',
        ],

        'add_rule_group' => [
            'label' => 'Қоида гуруҳини қўшиш',
        ],
    ],
];
