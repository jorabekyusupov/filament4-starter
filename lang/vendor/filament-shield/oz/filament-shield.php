<?php

return [
    /*
    |------------------------------------------------- -------------------------
    | Table Columns
    |------------------------------------------------- -------------------------
    */

    'column.name' => 'Ism',
    'column.guard_name' => 'Gvardiya nomi',
    'column.roles' => 'Rollar',
    'column.permissions' => 'Ruxsatlar',
    'column.updated_at' => 'Yangilangan',

    /*
    |------------------------------------------------- -------------------------
    | Form Fields
    |------------------------------------------------- -------------------------
    */

    'field.name' => 'Ism',
    'field.guard_name' => 'Gvardiya nomi',
    'field.permissions' => 'Ruxsatlar',
    'field.select_all.name' => 'Barchasini tanlash',
    'field.select_all.message' => 'Ushbu rol uchun <span class="text-primary font-medium">Mavjud</span> bo‘lgan barcha ruxsatlarni yoqish',

    /*
    |------------------------------------------------- -------------------------
    | Navigation & Resource
    |------------------------------------------------- -------------------------
    */

    'nav.group' => 'Sozlamalar',
    'nav.role.label' => 'Rollar',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rol',
    'resource.label.roles' => 'Rollar',

    /*
    |------------------------------------------------- -------------------------
    | Section & Tabs
    |------------------------------------------------- -------------------------
    */

    'section' => 'Mohiyatlar',
    'resources' => 'Resurslar',
    'widgets' => 'Vidjetlar',
    'pages' => 'Sahifalar',
    'custom' => 'Foydalanuvchi ruxsatlari',

    /*
    |------------------------------------------------- -------------------------
    | Messages
    |------------------------------------------------- -------------------------
    */

    'forbidden' => 'Sizda kirish huquqi yo‘q',

    /*
    |------------------------------------------------- -------------------------
    | Resource Permissions' Labels
    |------------------------------------------------- -------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Ko‘rish',
        'view_any' => 'Istalganini ko‘ra oladi',
        'create' => 'Yaratish',
        'update' => 'Yangilash',
        'delete' => 'O‘chirish',
        'delete_any' => 'Istalganini o‘chira oladi',
        'force_delete' => 'Majburiy o‘chirish',
        'force_delete_any' => 'Istalganini majburiy o‘chira oladi',
        'restore' => 'Tiklash',
        'reorder' => 'Tartibni o‘zgartirish',
        'restore_any' => 'Istalganini tiklay oladi',
        'replicate' => 'Nusxa olish',
    ],

];
