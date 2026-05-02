<?php

return [
    'platform_name' => 'منصة معا',
    'brand_primary' => 'منصة',
    'brand_secondary' => 'معا',
    'workspace' => 'مساحة العمل',

    'mobile' => [
        'toggle_navigation' => 'تبديل القائمة',
    ],

    'sidebar' => [
        'core' => 'الرئيسية',
        'operations' => 'التشغيل',
        'settings' => 'الإعدادات',
        'system' => 'النظام',
        'dashboard' => 'لوحة التحكم',
        'events' => 'الفعاليات',
        'forms' => 'النماذج',
        'invitations' => 'الدعوات',
        'qr_scanner' => 'ماسح QR',
        'attendance' => 'الحضور',
        'analytics' => 'التحليلات',
        'team' => 'الفريق',
        'branding' => 'الهوية البصرية',
        'billing' => 'الفوترة',
        'system_admin' => 'إدارة النظام',
        'end_impersonation' => 'إنهاء الانتحال',
        'plan' => 'الخطة',
        'ends' => 'ينتهي',
        'renews_at' => 'التجديد: :date',
        'sign_out' => 'تسجيل الخروج',
        'confirm_sign_out' => 'تأكيد تسجيل الخروج',
        'logout_hint' => 'سيتم إنهاء الجلسة الحالية فقط.',
    ],

    'dashboard' => [
        'title' => 'لوحة التحكم',
        'welcome_back' => 'مرحبًا بعودتك',
        'workspace_fallback' => 'مساحة الفعاليات',
        'active' => 'نشطة',
        'trial' => 'تجريبية',
        'days_left' => 'متبقي :count يوم',
        'trial_progress' => 'تقدم الفترة التجريبية',
        'kpi_total_events' => 'إجمالي الفعاليات',
        'kpi_invitations' => 'الدعوات',
        'kpi_qr_checkin' => 'تسجيل الدخول QR',
        'kpi_live' => 'مباشر',
        'kpi_plan' => 'الخطة',
        'manage_events' => 'إدارة الفعاليات',
        'view_all' => 'عرض الكل',
        'open_scanner' => 'فتح الماسح',
        'manage_billing' => 'إدارة الفوترة',
        'quick_actions' => 'إجراءات سريعة',
        'new_event' => 'فعالية جديدة',
        'analytics' => 'التحليلات',
        'team' => 'الفريق',
        'branding' => 'الهوية',
        'billing' => 'الفوترة',
        'recent_events' => 'آخر الفعاليات',
        'no_date' => 'بدون تاريخ',
        'location_tbd' => 'يحدد لاحقًا',
        'type_public' => 'عام',
        'type_private' => 'خاص',
        'type_pending' => 'معلق',
        'no_events_yet' => 'لا توجد فعاليات بعد',
        'create_first_event_hint' => 'أنشئ أول فعالية للبدء',
        'create_event' => 'إنشاء فعالية',
        'subscription' => 'الاشتراك',
        'plan_label' => 'خطة :plan',
        'trial_remaining' => 'متبقي :count يوم في الفترة التجريبية',
        'upgrade_now' => 'الترقية الآن',
        'plan_active' => 'خطة الاشتراك مفعلة',
        'renewal' => 'التجديد :date',
        'choose_plan' => 'اختيار خطة',
        'getting_started' => 'ابدأ الآن',
        'check_create_event' => 'أنشئ أول فعالية',
        'check_send_invites' => 'أرسل الدعوات',
        'check_setup_branding' => 'فعّل هوية البريد',
        'check_activate_subscription' => 'فعّل الاشتراك',
        'status_completed' => 'مكتمل',
        'status_pending' => 'قيد الانتظار',
        'workspace' => 'مساحة العمل',
        'company' => 'الشركة',
        'plan' => 'الخطة',
        'events' => 'الفعاليات',
        'invitations' => 'الدعوات',
        'trial_short' => 'تجريبي',
    ],

    'account' => [
        'title'                => 'إعدادات الحساب',
        'subtitle'             => 'حدّث بياناتك الشخصية، أمن حسابك، ومعلومات مؤسستك.',
        'kicker'               => 'الحساب',

        // التبويبات
        'tabs' => [
            'profile'  => 'حسابي',
            'security' => 'الأمان',
            'company'  => 'المؤسسة',
        ],

        // أقسام داخل التبويبات
        'sections' => [
            'profile_title'    => 'البيانات الشخصية',
            'profile_subtitle' => 'الاسم ورقم التواصل الذي يظهر داخل المنصة.',
            'email_title'      => 'البريد الإلكتروني',
            'email_subtitle'   => 'البريد المرتبط بحسابك. سيُطلب تأكيد كلمة المرور الحالية.',
            'password_title'   => 'كلمة المرور',
            'password_subtitle'=> 'يفضّل استخدام كلمة مرور قوية لم تُستخدم في حسابات أخرى.',
            'company_title'    => 'بيانات المؤسسة',
            'company_subtitle' => 'هذه البيانات تظهر في الفواتير، الفعاليات، ودعوات المرسلة.',
        ],

        // حقول النماذج
        'field' => [
            'name'                 => 'الاسم الكامل',
            'phone'                => 'رقم الجوال',
            'email'                => 'البريد الإلكتروني الجديد',
            'email_confirmation'   => 'تأكيد البريد الإلكتروني',
            'current_password'     => 'كلمة المرور الحالية',
            'new_password'         => 'كلمة المرور الجديدة',
            'new_password_confirm' => 'تأكيد كلمة المرور الجديدة',
            'company_name'         => 'اسم المؤسسة',
            'company_email'        => 'بريد المؤسسة (للتواصل)',
            'company_phone'        => 'هاتف المؤسسة',
            'subdomain'            => 'النطاق الفرعي',
            'timezone'             => 'المنطقة الزمنية',
        ],

        // أزرار
        'buttons' => [
            'save_profile'  => 'حفظ البيانات الشخصية',
            'save_email'    => 'تحديث البريد',
            'save_password' => 'تحديث كلمة المرور',
            'save_company'  => 'حفظ بيانات المؤسسة',
        ],

        // رسائل النجاح
        'flash' => [
            'profile_updated'  => 'تم تحديث بياناتك الشخصية بنجاح.',
            'email_updated'    => 'تم تحديث البريد الإلكتروني بنجاح.',
            'password_updated' => 'تم تحديث كلمة المرور بنجاح.',
            'company_updated'  => 'تم تحديث بيانات المؤسسة بنجاح.',
        ],

        // أخطاء
        'errors' => [
            'current_password_invalid' => 'كلمة المرور الحالية غير صحيحة.',
            'forbidden_company'        => 'لا تملك صلاحية تعديل بيانات المؤسسة. يجب أن تكون مالك الحساب.',
            'owner_only_hint'          => 'تعديل بيانات المؤسسة متاح لمالك الحساب فقط.',
        ],

        // تلميحات
        'hints' => [
            'subdomain_suffix' => '.maaninvite.com',
            'email_change_warning' => 'بعد التغيير ستحتاج لتأكيد البريد الجديد عند تسجيل الدخول التالي.',
            'password_min'     => '٨ أحرف على الأقل، ويفضّل خليط من الحروف والأرقام والرموز.',
        ],
    ],

    'team' => [
        // ── عام ──────────────────────────────────────────────────────────
        'title'               => 'إدارة الفريق',
        'subtitle'            => 'أضف أعضاء الفريق وحدّد صلاحياتهم للوصول إلى مساحة العمل.',
        'members_count'       => ':count عضو',
        'last_updated'        => 'آخر تحديث',

        // ── إضافة عضو ────────────────────────────────────────────────────
        'add_member'          => 'إضافة عضو جديد',
        'add_member_subtitle' => 'أدخل بيانات العضو وحدّد دوره في الفريق.',
        'field_name'          => 'الاسم الكامل',
        'field_email'         => 'البريد الإلكتروني',
        'field_phone'         => 'رقم الجوال',
        'field_role'          => 'الدور الوظيفي',
        'field_password'      => 'كلمة المرور المؤقتة',
        'placeholder_name'    => 'مثال: أحمد محمد',
        'placeholder_email'   => 'name@company.com',
        'placeholder_phone'   => '+966 5X XXX XXXX',
        'placeholder_password'=> '8 أحرف على الأقل',
        'btn_create'          => 'إنشاء الحساب',
        'btn_cancel'          => 'إلغاء',

        // ── جدول الأعضاء ─────────────────────────────────────────────────
        'col_member'          => 'العضو',
        'col_role'            => 'الدور',
        'col_phone'           => 'الجوال',
        'col_last_login'      => 'آخر دخول',
        'col_joined'          => 'تاريخ الانضمام',
        'col_actions'         => 'الإجراءات',
        'btn_save_role'       => 'حفظ',
        'btn_remove'          => 'إزالة',
        'never_logged_in'     => 'لم يسجّل دخولاً بعد',
        'you_badge'           => 'أنت',
        'no_members'          => 'لا يوجد أعضاء في الفريق بعد.',
        'no_members_hint'     => 'ابدأ بإضافة أول عضو باستخدام النموذج أعلاه.',

        // ── الأدوار ───────────────────────────────────────────────────────
        'role_organizer_owner'  => 'المالك',
        'role_organizer_admin'  => 'مدير الفعاليات',
        'role_operator'         => 'مشغّل',
        'role_validator'        => 'مدقق QR',
        'role_viewer'           => 'مشاهد',

        'role_desc_organizer_owner' => 'صلاحيات كاملة — لا يمكن تغيير دوره.',
        'role_desc_organizer_admin' => 'إنشاء الفعاليات والدعوات وإدارة الفريق.',
        'role_desc_operator'        => 'إدارة الدعوات وتسجيل الحضور.',
        'role_desc_validator'       => 'مسح رموز QR وتسجيل الدخول فقط.',
        'role_desc_viewer'          => 'عرض التقارير والإحصاءات فقط.',

        // ── حوار الحذف ────────────────────────────────────────────────────
        'confirm_remove_title'  => 'تأكيد إزالة العضو',
        'confirm_remove_body'   => 'هل أنت متأكد من إزالة <strong>:name</strong> من الفريق؟ سيُلغى وصوله فوراً.',
        'btn_confirm_remove'    => 'نعم، إزالة',

        // ── رسائل النجاح والخطأ ──────────────────────────────────────────
        'success_created'       => 'تم إنشاء حساب العضو بنجاح.',
        'success_role_updated'  => 'تم تحديث الدور بنجاح.',
        'success_removed'       => 'تم إزالة العضو من الفريق.',
        'error_own_account'     => 'لا يمكنك إزالة حسابك الخاص.',
        'error_no_company'      => 'مطلوب سياق الشركة.',

        // ── لوحة الأدوار ─────────────────────────────────────────────────
        'roles_legend'          => 'دليل الصلاحيات',
    ],

    'analytics' => [
        'title'                  => 'تحليلات الفعاليات',
        'subtitle'               => 'نظرة شاملة على الدعوات والحضور والاستجابات.',
        'last_updated'           => 'آخر تحديث',

        // ── فلاتر ──────────────────────────────────────────
        'filters_title'          => 'تصفية النتائج',
        'filter_event'           => 'الفعالية',
        'filter_event_all'       => 'جميع الفعاليات',
        'filter_date_from'       => 'من تاريخ',
        'filter_date_to'         => 'إلى تاريخ',
        'filter_apply'           => 'تطبيق',
        'filter_reset'           => 'إعادة تعيين',
        'showing_event'          => 'تعرض بيانات: :name',
        'showing_all'            => 'تعرض: جميع الفعاليات',
        'date_range'             => 'النطاق الزمني',

        // ── بطاقات KPI ────────────────────────────────────
        'kpi_total_invitations'  => 'إجمالي الدعوات',
        'kpi_confirmed'          => 'المقاعد المؤكدة',
        'kpi_attendance'         => 'الحضور الفعلي',
        'kpi_pending'            => 'في انتظار الرد',
        'kpi_sent_successfully'  => 'تم الإرسال بنجاح',
        'kpi_response_rate'      => 'معدل الاستجابة',
        'kpi_checkin_rate'       => 'معدل تسجيل الدخول',
        'kpi_awaiting_response'  => 'لم يُرد بعد',
        'kpi_acceptance_rate'    => 'معدل القبول',

        // ── تحليل الاستجابات ──────────────────────────────
        'response_breakdown'     => 'توزيع الاستجابات',
        'response_accepted'      => 'مقبولة',
        'response_declined'      => 'مرفوضة',
        'response_maybe'         => 'غير محددة',
        'response_pending'       => 'معلقة',

        // ── تحليل التذاكر ─────────────────────────────────
        'ticket_analysis'        => 'تحليل استخدام التذاكر',
        'ticket_main'            => 'المدعوون الرئيسيون',
        'ticket_guests'          => 'المرافقون',
        'ticket_checked_in'      => 'سجّل دخوله',
        'ticket_of'              => 'من',

        // ── منحنى الوصول ──────────────────────────────────
        'arrival_timeline'       => 'منحنى الوصول',
        'arrival_subtitle'       => 'توزيع تسجيل الدخول حسب ساعة الفعالية',
        'arrival_no_data'        => 'لا توجد بيانات حضور متاحة بعد.',
        'axis_hour'              => 'الساعة',
        'axis_count'             => 'عدد الحاضرين',

        // ── اتجاه الدعوات اليومي ──────────────────────────
        'daily_trend'            => 'اتجاه إرسال الدعوات',
        'daily_subtitle'         => 'عدد الدعوات المرسلة يوميًا خلال الفترة',
        'daily_no_data'          => 'لا توجد بيانات دعوات لهذه الفترة.',

        // ── حالات خاصة ────────────────────────────────────
        'no_data'                => 'لا توجد بيانات',
        'no_data_hint'           => 'اختر فعالية أو تحقق من الفترة الزمنية المحددة.',
        'export_pdf'             => 'تصدير PDF',
        'refresh'                => 'تحديث',
    ],

    'billing' => [
        'hero_kicker' => 'الفوترة والاشتراكات',
        'hero_title' => 'اختر الخطة المناسبة لنمو أعمالك',
        'hero_subtitle' => 'جميع الخطط تشمل تهيئة موجهة، وسيتواصل فريقنا معك لإكمال تفعيل الفوترة دون أي توقف.',
        'current_plan' => 'الخطة الحالية',
    ],

    // ── Auth pages (login / register visual panel) ────────────────
    'auth' => [
        'welcome'                  => 'أهلاً بك',
        'visual_title_default'     => 'أدر فعالياتك باحترافية من مكان واحد.',
        'visual_subtitle_default'  => 'دعوات ذكية، حضور فوري، هوية مميزة — كل ما تحتاجه في منصة SaaS واحدة.',
        'feat_invitations'         => 'دعوات ذكية',
        'feat_invitations_desc'    => 'أرسل دعوات مخصصة وتتبّع الردود لحظياً.',
        'feat_checkin'             => 'تسجيل حضور QR',
        'feat_checkin_desc'        => 'مسح فوري ودقيق عند بوابة الدخول.',
        'feat_analytics'           => 'تحليلات متقدمة',
        'feat_analytics_desc'      => 'نظرة شاملة على أداء كل فعالية.',
        'stat_delivery'            => 'استمرارية الإرسال',
        'stat_multitenant'         => 'بيئات معزولة',
        'stat_realtime'            => 'تحديثات فورية',
    ],
];
