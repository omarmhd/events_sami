<?php

return [
    'title' => 'إدارة الدعوات',
    'event_fallback' => 'جميع الفعاليات',

    'create' => [
        'page_title' => 'إرسال دعوة',
        'page_subtitle' => 'تنظيم دعوة الموظف وربطها بالفعالية من نفس نموذج المنصة الموحد.',
        'validation_title' => 'يوجد بيانات تحتاج مراجعة',
        'validation_hint' => 'يرجى تصحيح الحقول المحددة ثم إعادة الإرسال.',
        'fields' => [
            'event' => 'الفعالية',
            'event_placeholder' => 'اختر فعالية',
            'full_name' => 'الاسم الكامل',
            'full_name_placeholder' => 'مثال: أحمد علي',
            'email' => 'البريد الإلكتروني',
            'email_placeholder' => 'name@company.com',
            'position' => 'المسمى الوظيفي / الرقم الوظيفي',
            'position_placeholder' => 'مثال: أخصائي موارد بشرية',
            'nationality' => 'الجنسية',
            'allowed_guests' => 'عدد المرافقين المسموح',
            'allowed_guests_hint' => 'عدد الضيوف الإضافيين الذين يمكن للمُدعو اصطحابهم.',
        ],
        'actions' => [
            'cancel' => 'إلغاء',
            'submit' => 'إرسال الدعوة',
        ],
        'messages' => [
            'create_event_first' => 'قم بإنشاء فعالية أولًا قبل إرسال الدعوات.',
            'email_already_invited' => 'هذا البريد تمت دعوته مسبقًا للفعالية المحددة.',
            'invitation_sent' => 'تم إرسال الدعوة بنجاح.',
        ],
    ],

    'actions' => [
        'new' => 'دعوة جديدة',
        'export_csv' => 'تصدير CSV',
        'import_csv' => 'استيراد CSV',
        'bulk_resend_selected' => 'إعادة إرسال المحدد',
        'bulk_resend_all' => 'إعادة إرسال الكل',
        'import' => 'استيراد',
        'close' => 'إغلاق',
        'resend' => 'إعادة إرسال',
        'copy_link' => 'نسخ الرابط',
    ],

    'index' => [
        'search_placeholder' => 'ابحث بالاسم، البريد، الجوال، المسمى، الحالة',
        'search' => 'بحث',
        'overview' => 'نظرة عامة',
        'plan_gate_note' => 'الأدوات المتقدمة مرتبطة بالخطة: استيراد CSV وإعادة الإرسال الجماعي تتطلب باقة احترافية أو أعلى.',
        'upgrade_plan' => 'ترقية الخطة',
        'edit_invitation' => 'تعديل الدعوة',
        'copy_invite_message' => 'نسخ رسالة الدعوة',
        'more_options' => 'خيارات إضافية',
        'copy_tickets_link' => 'نسخ رابط التذاكر',
        'confirm_resend_email' => 'هل تريد إعادة إرسال الدعوة؟',
        'confirm_delete' => 'هل أنت متأكد من حذف هذه الدعوة؟ سيتم حذف البيانات المرتبطة بها.',
        'delete_invitation' => 'حذف الدعوة',
    ],

    'kpi' => [
        'total' => 'الإجمالي',
        'accepted' => 'مقبولة',
        'pending' => 'معلقة',
        'declined' => 'مرفوضة',
        'maybe' => 'ربما',
    ],

    'filters' => [
        'all' => 'الكل',
        'pending' => 'معلقة',
        'accepted' => 'مقبولة',
        'declined' => 'مرفوضة',
        'maybe' => 'ربما',
    ],

    'table' => [
        'name' => 'الاسم',
        'event' => 'الفعالية',
        'position' => 'المسمى الوظيفي',
        'guests' => 'الضيوف',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الجوال',
        'status' => 'الحالة',
        'sent_date' => 'تاريخ الإرسال',
        'responded' => 'وقت الرد',
        'actions' => 'الإجراءات',
        'no_phone' => '—',
        'no_event' => 'غير مرتبطة بفعالية',
        'no_position' => '—',
        'no_response' => '—',
        'empty_filtered' => 'لا توجد دعوات مطابقة للفلاتر الحالية',
    ],

    'status' => [
        'accepted' => 'مقبولة',
        'pending' => 'معلقة',
        'maybe' => 'ربما',
        'sent' => 'تم الإرسال',
        'declined' => 'مرفوضة',
        'rejected' => 'مرفوضة',
    ],

    'import' => [
        'label' => 'ملف CSV',
        'hint' => '(name, email, phone, nationality)',
    ],

    'js' => [
        'resend_success' => 'تمت إعادة الإرسال',
        'resend_failed' => 'تعذر إعادة الإرسال',
        'link_unavailable' => 'الرابط غير متاح',
        'link_copied' => 'تم نسخ الرابط',
        'copy_failed' => 'تعذر النسخ تلقائيًا',
        'copy_fetch_error' => 'حدث خطأ أثناء جلب الرابط',
        'select_one' => 'حدد دعوة واحدة على الأقل',
        'confirm_bulk_resend' => 'إعادة إرسال :count دعوة محددة؟',
        'confirm_resend_all_filtered' => 'إعادة إرسال جميع الدعوات ضمن الفلتر الحالي؟',
    ],

    'api' => [
        'feature_pro_only' => 'هذه الميزة متاحة فقط للباقات الاحترافية',
        'import_success' => 'تم استيراد :imported دعوة بنجاح (:skipped متخطاة)',
        'import_failed' => 'فشل الاستيراد: :error',
        'resend_success' => 'تم إعادة إرسال الدعوة بنجاح',
        'resend_failed' => 'فشل إعادة الإرسال',
        'bulk_resend_success' => 'تم إرسال :count دعوة',
        'copied' => 'تم نسخ الرابط',
        'link_text' => 'الرجاء الدخول على: :url',
    ],
];
