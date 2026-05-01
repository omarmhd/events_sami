<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Arabic Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'accepted'             => 'يجب قبول حقل :attribute.',
    'accepted_if'          => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url'           => 'حقل :attribute لا يمثل رابطاً صحيحاً.',
    'after'                => 'يجب على حقل :attribute أن يكون تاريخاً بعد :date.',
    'after_or_equal'       => 'يجب على حقل :attribute أن يكون تاريخاً بعد أو مساوياً للتاريخ :date.',
    'alpha'                => 'يجب أن لا يحتوي حقل :attribute سوى على حروف.',
    'alpha_dash'           => 'يجب أن لا يحتوي حقل :attribute سوى على حروف، أرقام، شرطات، وشرطات سفلية.',
    'alpha_num'            => 'يجب أن لا يحتوي حقل :attribute سوى على حروف وأرقام.',
    'array'                => 'يجب أن يكون حقل :attribute مصفوفة.',
    'before'               => 'يجب على حقل :attribute أن يكون تاريخاً سابقاً للتاريخ :date.',
    'before_or_equal'      => 'يجب على حقل :attribute أن يكون تاريخاً سابقاً أو مساوياً للتاريخ :date.',
    'between'              => [
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'file'    => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute بين :min و :max.',
        'array'   => 'يجب أن يحتوي :attribute على عدد عناصر بين :min و :max.',
    ],
    'boolean'              => 'يجب أن تكون قيمة حقل :attribute true أو false.',
    'confirmed'            => 'حقل تأكيد :attribute غير مطابق.',
    'current_password'     => 'كلمة المرور الحالية غير صحيحة.',
    'date'                 => 'حقل :attribute ليس تاريخاً صحيحاً.',
    'date_equals'          => 'يجب أن يكون حقل :attribute مساوياً للتاريخ :date.',
    'date_format'          => 'لا يتوافق حقل :attribute مع الشكل :format.',
    'declined'             => 'يجب رفض حقل :attribute.',
    'declined_if'          => 'يجب رفض حقل :attribute عندما يكون :other هو :value.',
    'different'            => 'يجب أن يكون حقلا :attribute و :other مختلفَين.',
    'digits'               => 'يجب أن يحتوي حقل :attribute على :digits رقماً.',
    'digits_between'       => 'يجب أن يحتوي حقل :attribute بين :min و :max رقماً.',
    'dimensions'           => 'الصورة في حقل :attribute غير صالحة.',
    'distinct'             => 'لحقل :attribute قيمة مكررة.',
    'email'                => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيحاً.',
    'ends_with'            => 'يجب أن ينتهي :attribute بأحد القيم التالية: :values.',
    'enum'                 => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'exists'               => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'file'                 => 'يجب أن يكون :attribute ملفاً.',
    'filled'               => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute أكبر من :value.',
        'array'   => 'يجب أن يحتوي :attribute على أكثر من :value عنصر.',
    ],
    'gte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute مساوياً أو أكبر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute مساوياً أو أكبر من :value.',
        'array'   => 'يجب أن يحتوي :attribute على :value عناصر أو أكثر.',
    ],
    'image'                => 'يجب أن يكون :attribute صورة.',
    'in'                   => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'in_array'             => 'حقل :attribute غير موجود في :other.',
    'integer'              => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'ip'                   => 'يجب أن يكون :attribute عنوان IP صحيحاً.',
    'ipv4'                 => 'يجب أن يكون :attribute عنوان IPv4 صحيحاً.',
    'ipv6'                 => 'يجب أن يكون :attribute عنوان IPv6 صحيحاً.',
    'json'                 => 'يجب أن يكون :attribute نصاً بصيغة JSON.',
    'lt'                   => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute أصغر من :value.',
        'array'   => 'يجب أن يحتوي :attribute على أقل من :value عنصر.',
    ],
    'lte'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'file'    => 'يجب أن يكون حجم الملف :attribute مساوياً أو أصغر من :value كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute مساوياً أو أصغر من :value.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :value عنصر.',
    ],
    'mac_address'          => 'يجب أن يكون :attribute عنوان MAC صحيحاً.',
    'max'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من أو تساوي :max.',
        'file'    => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'string'  => 'يجب أن لا يتجاوز عدد محارف :attribute :max محرفاً.',
        'array'   => 'يجب أن لا يحتوي :attribute على أكثر من :max عنصر.',
    ],
    'mimes'                => 'يجب أن يكون :attribute ملفاً من النوع: :values.',
    'mimetypes'            => 'يجب أن يكون :attribute ملفاً من النوع: :values.',
    'min'                  => [
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :min.',
        'file'    => 'يجب أن لا يقل حجم الملف :attribute عن :min كيلوبايت.',
        'string'  => 'يجب أن لا يقل عدد محارف :attribute عن :min محارف.',
        'array'   => 'يجب أن يحتوي :attribute على الأقل على :min عنصر.',
    ],
    'multiple_of'          => 'يجب أن تكون قيمة :attribute من مضاعفات :value.',
    'not_in'               => 'القيمة المختارة لحقل :attribute غير صالحة.',
    'not_regex'            => 'صيغة حقل :attribute غير صالحة.',
    'numeric'              => 'يجب أن يكون :attribute رقماً.',
    'password'             => 'كلمة المرور غير صحيحة.',
    'present'              => 'يجب أن يكون حقل :attribute موجوداً.',
    'prohibited'           => 'حقل :attribute محظور.',
    'prohibited_if'        => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless'    => 'حقل :attribute محظور إلا إذا كان :other في :values.',
    'prohibits'            => 'حقل :attribute يمنع وجود :other.',
    'regex'                => 'صيغة حقل :attribute غير صالحة.',
    'required'             => 'حقل :attribute مطلوب.',
    'required_array_keys'  => 'يجب أن يحتوي حقل :attribute على مدخلات لـ: :values.',
    'required_if'          => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless'      => 'حقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with'        => 'حقل :attribute مطلوب عند وجود :values.',
    'required_with_all'    => 'حقل :attribute مطلوب عند وجود :values.',
    'required_without'     => 'حقل :attribute مطلوب عند غياب :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند غياب جميع قيم :values.',
    'same'                 => 'يجب أن يتطابق حقلا :attribute و :other.',
    'size'                 => [
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'file'    => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'string'  => 'يجب أن يكون عدد محارف :attribute :size.',
        'array'   => 'يجب أن يحتوي :attribute على :size عنصر.',
    ],
    'starts_with'          => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values.',
    'string'               => 'يجب أن يكون :attribute نصاً.',
    'timezone'             => 'يجب أن يكون :attribute منطقة زمنية صحيحة.',
    'unique'               => 'قيمة :attribute مُستخدمة من قبل.',
    'uploaded'             => 'فشل رفع الملف :attribute.',
    'url'                  => 'صيغة رابط :attribute غير صحيحة.',
    'uuid'                 => 'يجب أن يكون :attribute UUID صحيحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Attribute Names
    |--------------------------------------------------------------------------
    | Map field keys to friendly Arabic labels so errors say
    | "الاسم الكامل مطلوب" instead of "حقل guest_name مطلوب".
    */

    'custom' => [
        'guest_name' => [
            'required' => 'الاسم الكامل مطلوب.',
            'string'   => 'يجب أن يكون الاسم الكامل نصاً.',
            'max'      => 'يجب ألا يتجاوز الاسم الكامل 255 حرفاً.',
        ],
        'guest_email' => [
            'required' => 'البريد الإلكتروني مطلوب.',
            'email'    => 'يجب أن يكون البريد الإلكتروني عنواناً صحيحاً.',
            'max'      => 'يجب ألا يتجاوز البريد الإلكتروني 255 حرفاً.',
        ],
    ],

    'attributes' => [
        'guest_name'  => 'الاسم الكامل',
        'guest_email' => 'البريد الإلكتروني',
        'name'        => 'الاسم',
        'email'       => 'البريد الإلكتروني',
        'password'    => 'كلمة المرور',
        'slug'        => 'الرابط المختصر',
        'headline'    => 'العنوان الرئيسي',
        'intro_text'  => 'النص التمهيدي',
        'is_active'   => 'الحالة',
        'decision'    => 'القرار',
    ],

];
