# setup-and-run.md - دليل البدء السريع لـ Maan Invite

## 🚀 خطوات التثبيت والتشغيل

### 1️⃣ **التثبيت الأولي**

```bash
# الدخول إلى مجلد المشروع
cd d:\2026\sami-events\ -\ Copy

# تثبيت المتطلبات
composer install

# نسخ ملف الإعدادات
copy .env.local .env

# توليد مفتاح التطبيق إذا لم يكن موجوداً
php artisan key:generate

# تثبيت المتطلبات الـ JavaScript
npm install

# بناء الـ CSS و JavaScript
npm run build
```

### 2️⃣ **إعداد قاعدة البيانات**

```bash
# إنشاء قاعدة البيانات (تأكد من وجود MySQL)
# يمكنك استخدام PHPMyAdmin أو command line

# تشغيل الـ migrations
php artisan migrate

# تشغيل الـ seeders (يملأ البيانات الأساسية)
php artisan db:seed

# أو تشغيل seeder محدد:
php artisan db:seed --class=SubscriptionPlanSeeder
```

### 3️⃣ **إعدادات البريد الإلكتروني**

للتطوير المحلي، استخدم **MailHog**:

```bash
# تشغيل MailHog (يجب أن يكون مثبتاً)
mailhog

# سيكون متاحاً على: http://localhost:1025 (SMTP)
# واجهة الويب: http://localhost:8025
```

أو في `.env`:
```
MAIL_MAILER=log
```

### 4️⃣ **بدء خادم التطوير**

```bash
# في terminal وحده:
php artisan serve

# سيكون متاحاً على: http://localhost:8000
```

### 5️⃣ **تشغيل Queue Workers** (للوظائف غير المتزامنة)

```bash
# في terminal منفصل:
php artisan queue:work

# أو مع تحديث تلقائي:
php artisan queue:work --daemon
```

---

## ✨ الوصول إلى لوحات التحكم

### 🏢 لوحة الـ Organizer (منظم الفعاليات)
```
URL: http://localhost:8000/organizer
المسار الكامل: http://organizer.localhost:8000
```

**الميزات المتاحة:**
- 📋 إدارة الفعاليات (CRUD سريع)
- 👥 إدارة الدعوات والمدعوين
- 📊 الإحصائيات المفصلة
- 💳 إدارة الاشتراك والترقية
- 🔍 ماسح QR الحي

### ⚙️ لوحة النظام (System Admin)
```
URL: http://localhost:8000/admin
```

**الميزات المتاحة:**
- 👨‍💼 إدارة المستخدمين
- 🏢 إدارة الشركات/المنظمين
- 💰 إدارة الفواتير والاشتراكات
- 🔑 إدارة الميزات

---

## 🔐 المسارات الأساسية

### التسجيل والمصادقة
```
POST /register/send-otp              → إرسال رمز OTP
POST /register/verify-otp            → التحقق من الرمز
GET  /register/profile-setup         → شاشة إعداد الملف الشخصي
POST /register/profile-setup         → حفظ الملف الشخصي
POST /logout                         → تسجيل الخروج
```

### الفعاليات والدعوات
```
GET  /organizer/events               → قائمة الفعاليات
POST /organizer/events               → إنشاء فعالية جديدة
GET  /organizer/invitations/{event}  → قائمة الدعوات
POST /organizer/invitations/import   → استيراد دعوات (CSV)
POST /organizer/invitations/resend   → إعادة إرسال دعوة
GET  /organizer/invitations/export   → تصدير CSV
```

### الدخول/الحضور
```
GET  /organizer/checkin/{eventSlug}  → صفحة ماسح QR
POST /organizer/checkin/process      → معالجة QR
GET  /organizer/checkin/stats        → الإحصائيات المباشرة
```

### الاشتراكات
```
GET  /organizer/subscription         → عرض الاشتراك الحالي
GET  /organizer/subscription/upgrade → صفحة الترقية
POST /organizer/subscription/upgrade → معالجة الترقية
GET  /organizer/subscription/paywall → صفحة المقفل
```

### الإحصائيات
```
GET  /analytics/company              → لوحة الشركة
GET  /analytics/events/{event}       → لوحة الفعالية
GET  /analytics/attendance-report    → تقرير الحضور
GET  /analytics/export               → تصدير التقارير
GET  /analytics/real-time            → الإحصائيات المباشرة
```

---

## 📊 البيانات الأولية (Seeders)

عند تشغيل `php artisan db:seed`، سيتم إنشاء:

### 1. خطط الاشتراك (SubscriptionPlan)
```php
[
    'Starter' => [
        'price' => $99,
        'annual_events_limit' => 12,
        'max_invites_per_event' => 100,
    ],
    'Professional' => [
        'price' => $299,
        'annual_events_limit' => 100,
        'max_invites_per_event' => 1000,
    ],
    'Enterprise' => [
        'price' => null,
        'annual_events_limit' => 99999,
        'max_invites_per_event' => 99999,
    ]
]
```

### 2. ميزات كل خطة
```php
'Starter' => ['basic_event_creation', 'manual_invites', 'basic_analytics'],
'Professional' => ['bulk_csv_import', 'custom_branding', 'api_access_basic'],
'Enterprise' => ['sso_integration', 'white_label', 'custom_development'],
```

---

## 🧪 اختبار التطبيق

### اختبار تدفق التسجيل
```
1. زر http://localhost:8000/register
2. أدخل بريدك الإلكتروني
3. انقر "إرسال رمز OTP"
4. تحقق من الـ MailHog على http://localhost:8025
5. انسخ رمز OTP (6 أرقام)
6. أدخله في الحقل
7. أكمل إعداد الملف الشخصي
```

### اختبار الفعاليات
```
1. سجل الدخول إلى /organizer
2. انقر "فعالية جديدة"
3. أدخل التفاصيل (الاسم، المكان، التاريخ)
4. اختر النوع: "خاص" أو "عام"
5. احفظ
```

### اختبار الدعوات
```
1. اذهب إلى "إدارة الدعوات"
2. اختر "استيراد CSV" أو أضف يدويًا
3. أرسل الدعوات
4. افحص البريد المرسل على MailHog
```

### اختبار QR
```
1. من متصفح أخر/جهاز، اذهب إلى رابط الفعالية العام
2. اضغط "تأكيد الحضور"
3. ستستقبل بريد بـ QR
4. اذهب إلى /organizer/checkin/{event}
5. امسح QR من البريد باستخدام الكاميرا
```

---

## 🔧 الإعدادات المهمة

### `.env` - الإعدادات الحرجة

```bash
# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=maaninvite
DB_USERNAME=root
DB_PASSWORD=

# البريد
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025

# الاشتراكات
SUBSCRIPTION_TRIAL_DAYS=15
SUBSCRIPTION_TRIAL_EVENTS=2
SUBSCRIPTION_TRIAL_INVITES=10

# OTP
OTP_EXPIRY_MINUTES=15
OTP_MAX_ATTEMPTS=5
OTP_LENGTH=6
```

### `config/subscription.php` - منطق الخطط

يحتوي على جميع مواصفات الخطط والميزات المتاحة.

### `config/payment.php` - إعدادات الدفع

جهز لتكامل Stripe / PayPal / Tap.

---

## 🚨 مشاكل شائعة والحل

### ❌ خطأ: "SQLSTATE[HY000]"
**الحل:** تأكد من تشغيل MySQL:
```bash
# Windows
mysql -u root -p

# أو من خدمات Windows
services.msc → ابحث عن MySQL
```

### ❌ خطأ: "No such file or directory"
**الحل:** تأكد من المسار:
```bash
# صحيح:
d:\2026\sami-events\ -\ Copy

# خاطئ:
d:\2026\sami-events
```

### ❌ خطأ: "CSRF token mismatch"
**الحل:** تأكد من تعريف `csrf_token` في `.env`:
```bash
# جرب المتصفح في خضع خاص (incognito)
# أو امسح الـ cache والـ cookies
```

### ❌ البريد لا يصل
**الحل:** استخدم MailHog:
```bash
# 1. ثبت MailHog من: https://github.com/mailhog/MailHog
# 2. شغّل: mailhog
# 3. افحص: http://localhost:8025
```

### ❌ QR لا يعمل
**الحل:** تأكد من:
- الكاميرا مفعّلة في المتصفح
- الإضاءة كافية
- رمز QR واضح ومرئي

---

## 📱 اختبار الجوال

للاختبار على الهاتف:

```bash
# 1. اكتشف عنوان IP الجهاز
ipconfig

# 2. استخدم في الهاتف (نفس الشبكة):
http://[YOUR_IP]:8000

# 3. السماح بالكاميرا في الإعدادات
```

---

## 🎯 الخطوات النهائية

بعد الانتهاء من الاختبار:

1. **اختبار شامل للميزات:**
   - ✅ التسجيل بـ OTP
   - ✅ إنشاء الفعاليات
   - ✅ إدارة الدعوات
   - ✅ ماسح QR
   - ✅ الإحصائيات
   - ✅ الاشتراكات

2. **الأمان:**
   - ✅ تغيير APP_KEY
   - ✅ تشفير البيانات الحساسة
   - ✅ تفعيل HTTPS

3. **الإنتاج:**
   - ✅ استخدام خادم حقيقي (Apache/Nginx)
   - ✅ تكامل Stripe للدفع الحقيقي
   - ✅ نسخة احتياطية لقاعدة البيانات
   - ✅ مراقبة الأداء (Monitoring)

---

## 📚 المراجع

- [Laravel Documentation](https://laravel.com/docs)
- [Filament Documentation](https://filamentphp.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [html5-qrcode](https://github.com/mebjas/html5-qrcode)

---

## 💬 للمساعدة

إذا واجهت أي مشاكل:

1. افحص ملف `storage/logs/laravel.log`
2. استخدم `php artisan tinker` للتشخيص
3. تحقق من `config/subscription.php` و `config/payment.php`

**تم الانتهاء من الإعداد! 🎉**
