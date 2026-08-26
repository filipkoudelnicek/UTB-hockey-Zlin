<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => ':attribute musí být přijat.',
    'accepted_if' => ':attribute musí být přijat, pokud :other je :value.',
    'active_url' => ':attribute musí být platná URL adresa.',
    'after' => ':attribute musí být datum po :date.',
    'after_or_equal' => ':attribute musí být datum po nebo rovno :date.',
    'alpha' => ':attribute smí obsahovat pouze písmena.',
    'alpha_dash' => ':attribute smí obsahovat pouze písmena, čísla, pomlčky a podtržítka.',
    'alpha_num' => ':attribute smí obsahovat pouze písmena a čísla.',
    'array' => ':attribute musí být pole.',
    'ascii' => ':attribute smí obsahovat pouze jedno-bajtové alfanumerické znaky a symboly.',
    'before' => ':attribute musí být datum před :date.',
    'before_or_equal' => ':attribute musí být datum před nebo rovno :date.',
    'between' => [
        'array' => ':attribute musí obsahovat mezi :min a :max položkami.',
        'file' => ':attribute musí být mezi :min a :max kilobajty.',
        'numeric' => ':attribute musí být mezi :min a :max.',
        'string' => ':attribute musí mít mezi :min a :max znaky.',
    ],
    'boolean' => ':attribute musí být pravda nebo nepravda.',
    'can' => ':attribute obsahuje nepovolenou hodnotu.',
    'confirmed' => 'Potvrzení pole :attribute nesouhlasí.',
    'contains' => ':attribute chybí požadovaná hodnota.',
    'current_password' => 'Heslo je nesprávné.',
    'date' => ':attribute musí být platné datum.',
    'date_equals' => ':attribute musí být datum rovné :date.',
    'date_format' => ':attribute neodpovídá formátu :format.',
    'decimal' => ':attribute musí mít :decimal desetinných míst.',
    'declined' => ':attribute musí být odmítnut.',
    'declined_if' => ':attribute musí být odmítnut, pokud :other je :value.',
    'different' => ':attribute a :other se musí lišit.',
    'digits' => ':attribute musí mít :digits číslic.',
    'digits_between' => ':attribute musí mít mezi :min a :max číslicemi.',
    'dimensions' => ':attribute má neplatné rozměry obrázku.',
    'distinct' => ':attribute obsahuje duplicitní hodnotu.',
    'doesnt_end_with' => ':attribute nesmí končit na: :values.',
    'doesnt_start_with' => ':attribute nesmí začínat na: :values.',
    'email' => ':attribute musí být platná e-mailová adresa.',
    'ends_with' => ':attribute musí končit jedním z následujících: :values.',
    'enum' => 'Vybraný :attribute je neplatný.',
    'exists' => 'Vybraný :attribute je neplatný.',
    'extensions' => ':attribute musí mít jednu z následujících přípon: :values.',
    'file' => ':attribute musí být soubor.',
    'filled' => ':attribute musí být vyplněn.',
    'gt' => [
        'array' => ':attribute musí mít více než :value položek.',
        'file' => ':attribute musí být větší než :value kilobajtů.',
        'numeric' => ':attribute musí být větší než :value.',
        'string' => ':attribute musí být delší než :value znaků.',
    ],
    'gte' => [
        'array' => ':attribute musí mít alespoň :value položek.',
        'file' => ':attribute musí být větší nebo rovno :value kilobajtů.',
        'numeric' => ':attribute musí být větší nebo rovno :value.',
        'string' => ':attribute musí být delší nebo rovno :value znaků.',
    ],
    'hex_color' => ':attribute musí být platná hexadecimální barva.',
    'image' => ':attribute musí být obrázek.',
    'in' => 'Vybraný :attribute je neplatný.',
    'in_array' => ':attribute musí existovat v :other.',
    'integer' => ':attribute musí být celé číslo.',
    'ip' => ':attribute musí být platná IP adresa.',
    'ipv4' => ':attribute musí být platná IPv4 adresa.',
    'ipv6' => ':attribute musí být platná IPv6 adresa.',
    'json' => ':attribute musí být platný JSON řetězec.',
    'list' => ':attribute musí být seznam.',
    'lowercase' => ':attribute musí být malými písmeny.',
    'lt' => [
        'array' => ':attribute musí mít méně než :value položek.',
        'file' => ':attribute musí být menší než :value kilobajtů.',
        'numeric' => ':attribute musí být menší než :value.',
        'string' => ':attribute musí být kratší než :value znaků.',
    ],
    'lte' => [
        'array' => ':attribute nesmí mít více než :value položek.',
        'file' => ':attribute musí být menší nebo rovno :value kilobajtů.',
        'numeric' => ':attribute musí být menší nebo rovno :value.',
        'string' => ':attribute musí být kratší nebo rovno :value znaků.',
    ],
    'mac_address' => ':attribute musí být platná MAC adresa.',
    'max' => [
        'array' => ':attribute nesmí mít více než :max položek.',
        'file' => ':attribute nesmí být větší než :max kilobajtů.',
        'numeric' => ':attribute nesmí být větší než :max.',
        'string' => ':attribute nesmí být delší než :max znaků.',
    ],
    'max_digits' => ':attribute nesmí mít více než :max číslic.',
    'mimes' => ':attribute musí být soubor typu: :values.',
    'mimetypes' => ':attribute musí být soubor typu: :values.',
    'min' => [
        'array' => ':attribute musí mít alespoň :min položek.',
        'file' => ':attribute musí mít alespoň :min kilobajtů.',
        'numeric' => ':attribute musí být alespoň :min.',
        'string' => ':attribute musí mít alespoň :min znaků.',
    ],
    'min_digits' => ':attribute musí mít alespoň :min číslic.',
    'missing' => ':attribute musí chybět.',
    'missing_if' => ':attribute musí chybět, pokud :other je :value.',
    'missing_unless' => ':attribute musí chybět, pokud :other není :value.',
    'missing_with' => ':attribute musí chybět, pokud je přítomen :values.',
    'missing_with_all' => ':attribute musí chybět, pokud jsou přítomny :values.',
    'multiple_of' => ':attribute musí být násobkem hodnoty :value.',
    'not_in' => 'Vybraný :attribute je neplatný.',
    'not_regex' => 'Formát :attribute je neplatný.',
    'numeric' => ':attribute musí být číslo.',
    'password' => [
        'letters' => ':attribute musí obsahovat alespoň jedno písmeno.',
        'mixed' => ':attribute musí obsahovat alespoň jedno velké a jedno malé písmeno.',
        'numbers' => ':attribute musí obsahovat alespoň jedno číslo.',
        'symbols' => ':attribute musí obsahovat alespoň jeden symbol.',
        'uncompromised' => 'Zadané :attribute se objevilo v úniku dat. Zvolte jiné.',
    ],
    'present' => ':attribute musí být přítomen.',
    'present_if' => ':attribute musí být přítomen, pokud :other je :value.',
    'present_unless' => ':attribute musí být přítomen, pokud :other není :value.',
    'present_with' => ':attribute musí být přítomen, pokud je přítomen :values.',
    'present_with_all' => ':attribute musí být přítomen, pokud jsou přítomny :values.',
    'prohibited' => ':attribute je zakázán.',
    'prohibited_if' => ':attribute je zakázán, pokud :other je :value.',
    'prohibited_if_accepted' => ':attribute je zakázán, pokud je :other přijat.',
    'prohibited_if_declined' => ':attribute je zakázán, pokud je :other odmítnut.',
    'prohibited_unless' => ':attribute je zakázán, pokud :other není v :values.',
    'prohibits' => ':attribute brání přítomnosti :other.',
    'regex' => 'Formát :attribute je neplatný.',
    'required' => ':attribute je povinný.',
    'required_array_keys' => ':attribute musí obsahovat položky: :values.',
    'required_if' => ':attribute je povinný, pokud :other je :value.',
    'required_if_accepted' => ':attribute je povinný, pokud je :other přijat.',
    'required_if_declined' => ':attribute je povinný, pokud je :other odmítnut.',
    'required_unless' => ':attribute je povinný, pokud :other není v :values.',
    'required_with' => ':attribute je povinný, pokud je přítomen :values.',
    'required_with_all' => ':attribute je povinný, pokud jsou přítomny :values.',
    'required_without' => ':attribute je povinný, pokud :values není přítomen.',
    'required_without_all' => ':attribute je povinný, pokud není přítomen žádný z :values.',
    'same' => ':attribute musí odpovídat :other.',
    'size' => [
        'array' => ':attribute musí obsahovat :size položek.',
        'file' => ':attribute musí mít :size kilobajtů.',
        'numeric' => ':attribute musí být :size.',
        'string' => ':attribute musí mít :size znaků.',
    ],
    'starts_with' => ':attribute musí začínat jedním z následujících: :values.',
    'string' => ':attribute musí být řetězec.',
    'timezone' => ':attribute musí být platné časové pásmo.',
    'unique' => ':attribute je již použit.',
    'uploaded' => 'Nahrání :attribute se nezdařilo.',
    'uppercase' => ':attribute musí být velkými písmeny.',
    'url' => ':attribute musí být platná URL adresa.',
    'ulid' => ':attribute musí být platné ULID.',
    'uuid' => ':attribute musí být platné UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'Jméno',
        'email' => 'E-mail',
        'message' => 'Zpráva',
        'phone' => 'Telefon',
    ],

];
