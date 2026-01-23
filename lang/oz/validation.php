<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validatsiya qatorlari
    |--------------------------------------------------------------------------
    |
    | Quyidagi qatorlar validator klassi tomonidan ishlatiladigan standart xato
    | xabarlarini o'z ichiga oladi. Ba'zi qoidalarning bir nechta ko'rinishi
    | mavjud, masalan, hajm (size) qoidalari. Bu xabarlarni o'zgartirishingiz mumkin.
    |
    */

    'accepted' => ':attribute qabul qilinishi kerak.',
    'accepted_if' => ':other :value bo\'lganda :attribute qabul qilinishi kerak.',
    'active_url' => ':attribute yaroqli URL bo\'lishi kerak.',
    'after' => ':attribute :date sanasidan keyingi sana bo\'lishi kerak.',
    'after_or_equal' => ':attribute :date sanasidan keyingi yoki unga teng sana bo\'lishi kerak.',
    'alpha' => ':attribute faqat harflardan iborat bo\'lishi kerak.',
    'alpha_dash' => ':attribute faqat harflar, sonlar, chiziqchalar va pastki chiziqlardan iborat bo\'lishi kerak.',
    'alpha_num' => ':attribute faqat harflar va sonlardan iborat bo\'lishi kerak.',
    'any_of' => ':attribute maydoni yaroqsiz.',
    'array' => ':attribute massiv (to\'plam) bo\'lishi kerak.',
    'ascii' => ':attribute faqat bir baytli alfanumerik belgilar va belgilarni o\'z ichiga olishi kerak.',
    'before' => ':attribute :date sanasidan oldingi sana bo\'lishi kerak.',
    'before_or_equal' => ':attribute :date sanasidan oldingi yoki unga teng sana bo\'lishi kerak.',
    'between' => [
        'array' => ':attribute da :min va :max orasida element bo\'lishi kerak.',
        'file' => ':attribute hajmi :min va :max kilobayt orasida bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :min va :max orasida bo\'lishi kerak.',
        'string' => ':attribute uzunligi :min va :max belgilar orasida bo\'lishi kerak.',
    ],
    'boolean' => ':attribute maydoni rost (true) yoki yolg\'on (false) bo\'lishi kerak.',
    'can' => ':attribute maydonida ruxsat etilmagan qiymat mavjud.',
    'confirmed' => ':attribute tasdig\'i mos kelmadi.',
    'contains' => ':attribute maydonida kerakli qiymat mavjud emas.',
    'current_password' => 'Parol noto\'g\'ri.',
    'date' => ':attribute yaroqli sana bo\'lishi kerak.',
    'date_equals' => ':attribute :date ga teng sana bo\'lishi kerak.',
    'date_format' => ':attribute :format formatiga mos kelishi kerak.',
    'decimal' => ':attribute :decimal o\'nlik kasr xonasiga ega bo\'lishi kerak.',
    'declined' => ':attribute rad etilishi kerak.',
    'declined_if' => ':other :value bo\'lganda :attribute rad etilishi kerak.',
    'different' => ':attribute va :other farqli bo\'lishi kerak.',
    'digits' => ':attribute :digits raqamdan iborat bo\'lishi kerak.',
    'digits_between' => ':attribute :min va :max oralig\'idagi raqamlardan iborat bo\'lishi kerak.',
    'dimensions' => ':attribute rasm o\'lchamlari yaroqsiz.',
    'distinct' => ':attribute maydoni takrorlanuvchi qiymatga ega.',
    'doesnt_contain' => ':attribute maydoni quyidagilarni o\'z ichiga olmasligi kerak: :values.',
    'doesnt_end_with' => ':attribute quyidagilardan biri bilan tugamasligi kerak: :values.',
    'doesnt_start_with' => ':attribute quyidagilardan biri bilan boshlanmasligi kerak: :values.',
    'email' => ':attribute yaroqli elektron pochta manzili bo\'lishi kerak.',
    'encoding' => ':attribute maydoni :encoding kodirovkasida bo\'lishi kerak.',
    'ends_with' => ':attribute quyidagilardan biri bilan tugashi kerak: :values.',
    'enum' => 'Tanlangan :attribute yaroqsiz.',
    'exists' => 'Tanlangan :attribute yaroqsiz.',
    'extensions' => ':attribute maydoni quyidagi kengaytmalardan biriga ega bo\'lishi kerak: :values.',
    'file' => ':attribute fayl bo\'lishi kerak.',
    'filled' => ':attribute maydoni to\'ldirilishi shart.',
    'gt' => [
        'array' => ':attribute dagi elementlar soni :value tadan ko\'p bo\'lishi kerak.',
        'file' => ':attribute hajmi :value kilobaytdan katta bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :value dan katta bo\'lishi kerak.',
        'string' => ':attribute uzunligi :value belgidan ko\'p bo\'lishi kerak.',
    ],
    'gte' => [
        'array' => ':attribute dagi elementlar soni :value ta yoki undan ko\'p bo\'lishi kerak.',
        'file' => ':attribute hajmi :value kilobayt yoki undan katta bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :value yoki undan katta bo\'lishi kerak.',
        'string' => ':attribute uzunligi :value belgi yoki undan ko\'p bo\'lishi kerak.',
    ],
    'hex_color' => ':attribute yaroqli o\'n oltilik (hex) rang bo\'lishi kerak.',
    'image' => ':attribute rasm bo\'lishi kerak.',
    'in' => 'Tanlangan :attribute yaroqsiz.',
    'in_array' => ':attribute qiymati :other da mavjud emas.',
    'in_array_keys' => ':attribute maydoni quyidagi kalitlardan kamida bittasini o\'z ichiga olishi kerak: :values.',
    'integer' => ':attribute butun son bo\'lishi kerak.',
    'ip' => ':attribute yaroqli IP manzil bo\'lishi kerak.',
    'ipv4' => ':attribute yaroqli IPv4 manzil bo\'lishi kerak.',
    'ipv6' => ':attribute yaroqli IPv6 manzil bo\'lishi kerak.',
    'json' => ':attribute yaroqli JSON qatori bo\'lishi kerak.',
    'list' => ':attribute maydoni ro\'yxat bo\'lishi kerak.',
    'lowercase' => ':attribute kichik harflarda bo\'lishi kerak.',
    'lt' => [
        'array' => ':attribute dagi elementlar soni :value tadan kam bo\'lishi kerak.',
        'file' => ':attribute hajmi :value kilobaytdan kichik bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :value dan kichik bo\'lishi kerak.',
        'string' => ':attribute uzunligi :value belgidan kam bo\'lishi kerak.',
    ],
    'lte' => [
        'array' => ':attribute dagi elementlar soni :value tadan oshmasligi kerak.',
        'file' => ':attribute hajmi :value kilobayt yoki undan kichik bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :value yoki undan kichik bo\'lishi kerak.',
        'string' => ':attribute uzunligi :value belgi yoki undan kam bo\'lishi kerak.',
    ],
    'mac_address' => ':attribute yaroqli MAC manzil bo\'lishi kerak.',
    'max' => [
        'array' => ':attribute dagi elementlar soni :max tadan oshmasligi kerak.',
        'file' => ':attribute hajmi :max kilobaytdan oshmasligi kerak.',
        'numeric' => ':attribute qiymati :max dan oshmasligi kerak.',
        'string' => ':attribute uzunligi :max belgidan oshmasligi kerak.',
    ],
    'max_digits' => ':attribute :max raqamdan oshmasligi kerak.',
    'mimes' => ':attribute quyidagi turdagi fayl bo\'lishi kerak: :values.',
    'mimetypes' => ':attribute quyidagi turdagi fayl bo\'lishi kerak: :values.',
    'min' => [
        'array' => ':attribute da kamida :min ta element bo\'lishi kerak.',
        'file' => ':attribute hajmi kamida :min kilobayt bo\'lishi kerak.',
        'numeric' => ':attribute qiymati kamida :min bo\'lishi kerak.',
        'string' => ':attribute uzunligi kamida :min belgidan iborat bo\'lishi kerak.',
    ],
    'min_digits' => ':attribute kamida :min raqamdan iborat bo\'lishi kerak.',
    'missing' => ':attribute maydoni mavjud bo\'lmasligi kerak.',
    'missing_if' => ':other :value bo\'lganda :attribute maydoni mavjud bo\'lmasligi kerak.',
    'missing_unless' => ':other :value bo\'lmasa, :attribute maydoni mavjud bo\'lmasligi kerak.',
    'missing_with' => ':values mavjud bo\'lganda :attribute maydoni mavjud bo\'lmasligi kerak.',
    'missing_with_all' => ':values mavjud bo\'lganda :attribute maydoni mavjud bo\'lmasligi kerak.',
    'multiple_of' => ':attribute :value ga karrali bo\'lishi kerak.',
    'not_in' => 'Tanlangan :attribute yaroqsiz.',
    'not_regex' => ':attribute formati yaroqsiz.',
    'numeric' => ':attribute son bo\'lishi kerak.',
    'password' => [
        'letters' => ':attribute kamida bitta harfni o\'z ichiga olishi kerak.',
        'mixed' => ':attribute kamida bitta katta va bitta kichik harfni o\'z ichiga olishi kerak.',
        'numbers' => ':attribute kamida bitta raqamni o\'z ichiga olishi kerak.',
        'symbols' => ':attribute kamida bitta belgini o\'z ichiga olishi kerak.',
        'uncompromised' => 'Berilgan :attribute ma\'lumotlar sizib chiqishida ishtirok etgan. Iltimos, boshqa :attribute tanlang.',
    ],
    'present' => ':attribute maydoni mavjud bo\'lishi kerak.',
    'present_if' => ':other :value bo\'lganda :attribute maydoni mavjud bo\'lishi kerak.',
    'present_unless' => ':other :value bo\'lmasa, :attribute maydoni mavjud bo\'lishi kerak.',
    'present_with' => ':values mavjud bo\'lganda :attribute maydoni mavjud bo\'lishi kerak.',
    'present_with_all' => ':values mavjud bo\'lganda :attribute maydoni mavjud bo\'lishi kerak.',
    'prohibited' => ':attribute maydoni taqiqlangan.',
    'prohibited_if' => ':other :value bo\'lganda :attribute maydoni taqiqlangan.',
    'prohibited_if_accepted' => ':other qabul qilinganda :attribute maydoni taqiqlangan.',
    'prohibited_if_declined' => ':other rad etilganda :attribute maydoni taqiqlangan.',
    'prohibited_unless' => ':other :values ichida bo\'lmasa, :attribute maydoni taqiqlangan.',
    'prohibits' => ':attribute maydoni :other ning mavjud bo\'lishini taqiqlaydi.',
    'regex' => ':attribute formati yaroqsiz.',
    'required' => ':attribute maydoni to\'ldirilishi shart.',
    'required_array_keys' => ':attribute maydoni quyidagilar uchun yozuvlarni o\'z ichiga olishi kerak: :values.',
    'required_if' => ':other :value bo\'lganda :attribute maydoni to\'ldirilishi shart.',
    'required_if_accepted' => ':other qabul qilinganda :attribute maydoni to\'ldirilishi shart.',
    'required_if_declined' => ':other rad etilganda :attribute maydoni to\'ldirilishi shart.',
    'required_unless' => ':other :values ichida bo\'lmasa, :attribute maydoni to\'ldirilishi shart.',
    'required_with' => ':values mavjud bo\'lganda :attribute maydoni to\'ldirilishi shart.',
    'required_with_all' => ':values mavjud bo\'lganda :attribute maydoni to\'ldirilishi shart.',
    'required_without' => ':values mavjud bo\'lmaganda :attribute maydoni to\'ldirilishi shart.',
    'required_without_all' => ':values lardan hech biri mavjud bo\'lmaganda :attribute maydoni to\'ldirilishi shart.',
    'same' => ':attribute va :other mos kelishi kerak.',
    'size' => [
        'array' => ':attribute da :size ta element bo\'lishi kerak.',
        'file' => ':attribute hajmi :size kilobayt bo\'lishi kerak.',
        'numeric' => ':attribute qiymati :size ga teng bo\'lishi kerak.',
        'string' => ':attribute uzunligi :size belgidan iborat bo\'lishi kerak.',
    ],
    'starts_with' => ':attribute quyidagilardan biri bilan boshlanishi kerak: :values.',
    'string' => ':attribute qator (string) bo\'lishi kerak.',
    'timezone' => ':attribute yaroqli vaqt mintaqasi bo\'lishi kerak.',
    'unique' => ':attribute allaqachon band qilingan.',
    'uploaded' => ':attribute yuklanmadi.',
    'uppercase' => ':attribute katta harflarda bo\'lishi kerak.',
    'url' => ':attribute yaroqli URL bo\'lishi kerak.',
    'ulid' => ':attribute yaroqli ULID bo\'lishi kerak.',
    'uuid' => ':attribute yaroqli UUID bo\'lishi kerak.',

    /*
    |--------------------------------------------------------------------------
    | Maxsus Validatsiya Qatorlari
    |--------------------------------------------------------------------------
    |
    | Bu yerda siz atributlar uchun "attribute.rule" konventsiyasidan foydalangan
    | holda maxsus validatsiya xabarlarini belgilashingiz mumkin.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maxsus Validatsiya Atributlari
    |--------------------------------------------------------------------------
    |
    | Quyidagi qatorlar atributlarning nomini o'qishga qulayroq qilish uchun
    | ishlatiladi. Masalan, "email" o'rniga "Elektron pochta manzili".
    |
    */

    'attributes' => [],

];