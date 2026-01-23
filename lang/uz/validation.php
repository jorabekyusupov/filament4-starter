<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Валидация қаторлари
    |--------------------------------------------------------------------------
    |
    | Қуйидаги қаторлар валидатор класси томонидан ишлатиладиган стандарт хато
    | хабарларини ўз ичига олади. Баъзи қоидаларнинг бир нечта кўриниши
    | мавжуд, масалан, ҳажм (size) қоидалари. Бу хабарларни ўзгартиришингиз мумкин.
    |
    */

    'accepted' => ':attribute қабул қилиниши керак.',
    'accepted_if' => ':other :value бўлганда :attribute қабул қилиниши керак.',
    'active_url' => ':attribute яроқли URL бўлиши керак.',
    'after' => ':attribute :date санасидан кейинги сана бўлиши керак.',
    'after_or_equal' => ':attribute :date санасидан кейинги ёки унга тенг сана бўлиши керак.',
    'alpha' => ':attribute фақат ҳарфлардан иборат бўлиши керак.',
    'alpha_dash' => ':attribute фақат ҳарфлар, сонлар, чизиқчалар ва пастки чизиқлардан иборат бўлиши керак.',
    'alpha_num' => ':attribute фақат ҳарфлар ва сонлардан иборат бўлиши керак.',
    'any_of' => ':attribute майдони яроқсиз.',
    'array' => ':attribute массив (тўплам) бўлиши керак.',
    'ascii' => ':attribute фақат бир байтли алфанумерик белгилар ва белгиларни ўз ичига олиши керак.',
    'before' => ':attribute :date санасидан олдинги сана бўлиши керак.',
    'before_or_equal' => ':attribute :date санасидан олдинги ёки унга тенг сана бўлиши керак.',
    'between' => [
        'array' => ':attribute да :min ва :max орасида элемент бўлиши керак.',
        'file' => ':attribute ҳажми :min ва :max килобайт орасида бўлиши керак.',
        'numeric' => ':attribute қиймати :min ва :max орасида бўлиши керак.',
        'string' => ':attribute узунлиги :min ва :max белгилар орасида бўлиши керак.',
    ],
    'boolean' => ':attribute майдони рост (true) ёки ёлғон (false) бўлиши керак.',
    'can' => ':attribute майдонида рухсат этилмаган қиймат мавжуд.',
    'confirmed' => ':attribute тасдиғи мос келмади.',
    'contains' => ':attribute майдонида керакли қиймат мавжуд эмас.',
    'current_password' => 'Парол нотўғри.',
    'date' => ':attribute яроқли сана бўлиши керак.',
    'date_equals' => ':attribute :date га тенг сана бўлиши керак.',
    'date_format' => ':attribute :format форматига мос келиши керак.',
    'decimal' => ':attribute :decimal ўнлик каср хонасига эга бўлиши керак.',
    'declined' => ':attribute рад этилиши керак.',
    'declined_if' => ':other :value бўлганда :attribute рад этилиши керак.',
    'different' => ':attribute ва :other фарқли бўлиши керак.',
    'digits' => ':attribute :digits рақамдан иборат бўлиши керак.',
    'digits_between' => ':attribute :min ва :max оралиғидаги рақамлардан иборат бўлиши керак.',
    'dimensions' => ':attribute расм ўлчамлари яроқсиз.',
    'distinct' => ':attribute майдони такрорланувчи қийматга эга.',
    'doesnt_contain' => ':attribute майдони қуйидагиларни ўз ичига олмаслиги керак: :values.',
    'doesnt_end_with' => ':attribute қуйидагилардан бири билан тугамаслиги керак: :values.',
    'doesnt_start_with' => ':attribute қуйидагилардан бири билан бошланмаслиги керак: :values.',
    'email' => ':attribute яроқли электрон почта манзили бўлиши керак.',
    'encoding' => ':attribute майдони :encoding кодировкасида бўлиши керак.',
    'ends_with' => ':attribute қуйидагилардан бири билан тугаши керак: :values.',
    'enum' => 'Танланган :attribute яроқсиз.',
    'exists' => 'Танланган :attribute яроқсиз.',
    'extensions' => ':attribute майдони қуйидаги кенгайтмалардан бирига эга бўлиши керак: :values.',
    'file' => ':attribute файл бўлиши керак.',
    'filled' => ':attribute майдони тўлдирилиши шарт.',
    'gt' => [
        'array' => ':attribute даги элементлар сони :value тадан кўп бўлиши керак.',
        'file' => ':attribute ҳажми :value килобайтдан катта бўлиши керак.',
        'numeric' => ':attribute қиймати :value дан катта бўлиши керак.',
        'string' => ':attribute узунлиги :value белгидан кўп бўлиши керак.',
    ],
    'gte' => [
        'array' => ':attribute даги элементлар сони :value та ёки ундан кўп бўлиши керак.',
        'file' => ':attribute ҳажми :value килобайт ёки ундан катта бўлиши керак.',
        'numeric' => ':attribute қиймати :value ёки ундан катта бўлиши керак.',
        'string' => ':attribute узунлиги :value белги ёки ундан кўп бўлиши керак.',
    ],
    'hex_color' => ':attribute яроқли ўн олтилик (hex) ранг бўлиши керак.',
    'image' => ':attribute расм бўлиши керак.',
    'in' => 'Танланган :attribute яроқсиз.',
    'in_array' => ':attribute қиймати :other да мавжуд эмас.',
    'in_array_keys' => ':attribute майдони қуйидаги калитлардан камида биттасини ўз ичига олиши керак: :values.',
    'integer' => ':attribute бутун сон бўлиши керак.',
    'ip' => ':attribute яроқли IP манзил бўлиши керак.',
    'ipv4' => ':attribute яроқли IPv4 манзил бўлиши керак.',
    'ipv6' => ':attribute яроқли IPv6 манзил бўлиши керак.',
    'json' => ':attribute яроқли JSON қатори бўлиши керак.',
    'list' => ':attribute майдони рўйхат бўлиши керак.',
    'lowercase' => ':attribute кичик ҳарфларда бўлиши керак.',
    'lt' => [
        'array' => ':attribute даги элементлар сони :value тадан кам бўлиши керак.',
        'file' => ':attribute ҳажми :value килобайтдан кичик бўлиши керак.',
        'numeric' => ':attribute қиймати :value дан кичик бўлиши керак.',
        'string' => ':attribute узунлиги :value белгидан кам бўлиши керак.',
    ],
    'lte' => [
        'array' => ':attribute даги элементлар сони :value тадан ошмаслиги керак.',
        'file' => ':attribute ҳажми :value килобайт ёки ундан кичик бўлиши керак.',
        'numeric' => ':attribute қиймати :value ёки ундан кичик бўлиши керак.',
        'string' => ':attribute узунлиги :value белги ёки ундан кам бўлиши керак.',
    ],
    'mac_address' => ':attribute яроқли MAC манзил бўлиши керак.',
    'max' => [
        'array' => ':attribute даги элементлар сони :max тадан ошмаслиги керак.',
        'file' => ':attribute ҳажми :max килобайтдан ошмаслиги керак.',
        'numeric' => ':attribute қиймати :max дан ошмаслиги керак.',
        'string' => ':attribute узунлиги :max белгидан ошмаслиги керак.',
    ],
    'max_digits' => ':attribute :max рақамдан ошмаслиги керак.',
    'mimes' => ':attribute қуйидаги турдаги файл бўлиши керак: :values.',
    'mimetypes' => ':attribute қуйидаги турдаги файл бўлиши керак: :values.',
    'min' => [
        'array' => ':attribute да камида :min та элемент бўлиши керак.',
        'file' => ':attribute ҳажми камида :min килобайт бўлиши керак.',
        'numeric' => ':attribute қиймати камида :min бўлиши керак.',
        'string' => ':attribute узунлиги камида :min белгидан иборат бўлиши керак.',
    ],
    'min_digits' => ':attribute камида :min рақамдан иборат бўлиши керак.',
    'missing' => ':attribute майдони мавжуд бўлмаслиги керак.',
    'missing_if' => ':other :value бўлганда :attribute майдони мавжуд бўлмаслиги керак.',
    'missing_unless' => ':other :value бўлмаса, :attribute майдони мавжуд бўлмаслиги керак.',
    'missing_with' => ':values мавжуд бўлганда :attribute майдони мавжуд бўлмаслиги керак.',
    'missing_with_all' => ':values мавжуд бўлганда :attribute майдони мавжуд бўлмаслиги керак.',
    'multiple_of' => ':attribute :value га каррали бўлиши керак.',
    'not_in' => 'Танланган :attribute яроқсиз.',
    'not_regex' => ':attribute формати яроқсиз.',
    'numeric' => ':attribute сон бўлиши керак.',
    'password' => [
        'letters' => ':attribute камида битта ҳарфни ўз ичига олиши керак.',
        'mixed' => ':attribute камида битта катта ва битта кичик ҳарфни ўз ичига олиши керак.',
        'numbers' => ':attribute камида битта рақамни ўз ичига олиши керак.',
        'symbols' => ':attribute камида битта белгини ўз ичига олиши керак.',
        'uncompromised' => 'Берилган :attribute маълумотлар сизиб чиқишида иштирок этган. Илтимос, бошқа :attribute танланг.',
    ],
    'present' => ':attribute майдони мавжуд бўлиши керак.',
    'present_if' => ':other :value бўлганда :attribute майдони мавжуд бўлиши керак.',
    'present_unless' => ':other :value бўлмаса, :attribute майдони мавжуд бўлиши керак.',
    'present_with' => ':values мавжуд бўлганда :attribute майдони мавжуд бўлиши керак.',
    'present_with_all' => ':values мавжуд бўлганда :attribute майдони мавжуд бўлиши керак.',
    'prohibited' => ':attribute майдони тақиқланган.',
    'prohibited_if' => ':other :value бўлганда :attribute майдони тақиқланган.',
    'prohibited_if_accepted' => ':other қабул қилинганда :attribute майдони тақиқланган.',
    'prohibited_if_declined' => ':other рад этилганда :attribute майдони тақиқланган.',
    'prohibited_unless' => ':other :values ичида бўлмаса, :attribute майдони тақиқланган.',
    'prohibits' => ':attribute майдони :other нинг мавжуд бўлишини тақиқлайди.',
    'regex' => ':attribute формати яроқсиз.',
    'required' => ':attribute майдони тўлдирилиши шарт.',
    'required_array_keys' => ':attribute майдони қуйидагилар учун ёзувларни ўз ичига олиши керак: :values.',
    'required_if' => ':other :value бўлганда :attribute майдони тўлдирилиши шарт.',
    'required_if_accepted' => ':other қабул қилинганда :attribute майдони тўлдирилиши шарт.',
    'required_if_declined' => ':other рад этилганда :attribute майдони тўлдирилиши шарт.',
    'required_unless' => ':other :values ичида бўлмаса, :attribute майдони тўлдирилиши шарт.',
    'required_with' => ':values мавжуд бўлганда :attribute майдони тўлдирилиши шарт.',
    'required_with_all' => ':values мавжуд бўлганда :attribute майдони тўлдирилиши шарт.',
    'required_without' => ':values мавжуд бўлмаганда :attribute майдони тўлдирилиши шарт.',
    'required_without_all' => ':values лардан ҳеч бири мавжуд бўлмаганда :attribute майдони тўлдирилиши шарт.',
    'same' => ':attribute ва :other мос келиши керак.',
    'size' => [
        'array' => ':attribute да :size та элемент бўлиши керак.',
        'file' => ':attribute ҳажми :size килобайт бўлиши керак.',
        'numeric' => ':attribute қиймати :size га тенг бўлиши керак.',
        'string' => ':attribute узунлиги :size белгидан иборат бўлиши керак.',
    ],
    'starts_with' => ':attribute қуйидагилардан бири билан бошланиши керак: :values.',
    'string' => ':attribute қатор (string) бўлиши керак.',
    'timezone' => ':attribute яроқли вақт минтақаси бўлиши керак.',
    'unique' => ':attribute аллақачон банд қилинган.',
    'uploaded' => ':attribute юкланмади.',
    'uppercase' => ':attribute катта ҳарфларда бўлиши керак.',
    'url' => ':attribute яроқли URL бўлиши керак.',
    'ulid' => ':attribute яроқли ULID бўлиши керак.',
    'uuid' => ':attribute яроқли UUID бўлиши керак.',

    /*
    |--------------------------------------------------------------------------
    | Махсус Валидация Қаторлари
    |--------------------------------------------------------------------------
    |
    | Бу ерда сиз атрибутлар учун "attribute.rule" конвенциясидан фойдаланган
    | ҳолда махсус валидация хабарларини белгилашингиз мумкин.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Махсус Валидация Атрибутлари
    |--------------------------------------------------------------------------
    |
    | Қуйидаги қаторлар атрибутларнинг номини ўқишга қулайроқ қилиш учун
    | ишлатилади. Масалан, "email" ўрнига "Электрон почта манзили".
    |
    */

    'attributes' => [],

];