<?php

return [
    'label' => 'Faollik jurnali',
    'plural_label' => 'Faollik jurnallari',
    'navigation_group' => 'Ma\'muriyat',
    'table' => [
        'column' => [
            'log_name' => 'Jurnal nomi',
            'event' => 'Hodisa',
            'subject_id' => 'Subyekt ID',
            'subject_type' => 'Subyekt turi',
            'causer_id' => 'Tashabbuskor ID',
            'causer_type' => 'Tashabbuskor turi',
            'properties' => 'Xususiyatlar',
            'created_at' => 'Yaratilgan vaqti',
            'updated_at' => 'Yangilangan vaqti',
            'description' => 'Tavsif',
            'subject' => 'Subyekt',
            'causer' => 'Tashabbuskor',
            'ip_address' => 'IP manzil',
            'browser' => 'Brauzer',
        ],
        'filter' => [
            'event' => 'Hodisa',
            'created_at' => 'Yaratilgan vaqti',
            'created_from' => 'Dan boshlab',
            'created_until' => 'Gacha',
            'causer' => 'Tashabbuskor',
            'subject_type' => 'Subyekt turi',
        ],
    ],
    'infolist' => [
        'section' => [
            'activity_details' => 'Faollik tafsilotlari',
        ],
        'tab' => [
            'overview' => 'Umumiy ko\'rinish',
            'changes' => 'O\'zgarishlar',
            'raw_data' => 'Asl ma\'lumotlar',
            'old' => 'Eski',
            'new' => 'Yangi',
        ],
        'entry' => [
            'log_name' => 'Jurnal nomi',
            'event' => 'Hodisa',
            'created_at' => 'Yaratilgan vaqti',
            'description' => 'Tavsif',
            'subject' => 'Subyekt',
            'causer' => 'Tashabbuskor',
            'ip_address' => 'IP manzil',
            'browser' => 'Brauzer',
            'attributes' => 'Atributlar',
            'old' => 'Eski',
            'key' => 'Kalit',
            'value' => 'Qiymat',
            'properties' => 'Xususiyatlar',
        ],
    ],
    'action' => [
        'timeline' => [
            'label' => 'Xronologiya',
            'empty_state_title' => 'Faollik jurnallari topilmadi',
            'empty_state_description' => 'Ushbu yozuv uchun hali hech qanday harakat qayd etilmagan.',
        ],
        'delete' => [
            'confirmation' => 'Haqiqatan ham ushbu faollik jurnalini o\'chirib tashlamoqchimisiz? Bu amalni bekor qilib bo\'lmaydi.',
            'heading' => 'Jurnal yozuvini o\'chirish',
            'button' => 'O\'chirish',
        ],
        'revert' => [
            'heading' => 'O\'zgarishlarni bekor qilish',
            'confirmation' => 'Ushbu o\'zgarishni bekor qilmoqchimisiz? Bu eski qiymatlarni qayta tiklaydi.',
            'button' => 'Bekor qilish (Qaytarish)',
            'success' => 'O\'zgarishlar muvaffaqiyatli bekor qilindi',
            'no_old_data' => 'Qayta tiklash uchun eski ma\'lumotlar mavjud emas',
            'subject_not_found' => 'Subyekt modeli topilmadi',
        ],
        'export' => [
            'filename' => 'faollik_jurnallari',
            'notification' => [
                'completed' => 'Faollik jurnali eksporti yakunlandi, :successful_rows :rows_label eksport qilindi.',
            ],
        ],
    ],
    'filters' => 'Filtrlar',
    'pages' => [
        'user_activities' => [
            'title' => 'Foydalanuvchi faolligi',
            'heading' => 'Foydalanuvchi faolligi',
            'description_title' => 'Foydalanuvchi harakatlarini kuzatish',
            'description' => 'Ilovangizdagi foydalanuvchilar tomonidan bajarilgan barcha harakatlarni ko\'rish. To\'liq xronologiyani ko\'rish uchun foydalanuvchi, hodisa turi yoki subyekt bo\'yicha filtrlang.',
        ],
    ],
    'event' => [
        'created' => 'Yaratildi',
        'updated' => 'Yangilandi',
        'deleted' => 'O\'chirildi',
        'restored' => 'Qayta tiklandi',
    ],
    'filter' => [
        'causer' => 'Foydalanuvchi',
        'event' => 'Hodisa turi',
        'subject_type' => 'Subyekt turi',
    ],
    'widgets' => [
        'latest_activity' => 'So\'nggi faollik',
    ],
];