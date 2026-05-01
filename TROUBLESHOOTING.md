# TROUBLESHOOTING.md - دليل حل المشاكل

## 🔴 المشاكل الشائعة والحلول

---

## 1️⃣ مشاكل قاعدة البيانات (Database)

### ❌ خطأ: "SQLSTATE[HY000] [2002] No such file or directory"

**السبب:** قاعدة البيانات غير متصلة

**الحل:**
```bash
# 1. تحقق من running MySQL
services.msc → ابحث عن MySQL

# 2. أو شغّل من command line
net start MySQL80  # Windows الخدمات

# 3. أو من command line مباشرة
mysql -u root -p  # اختبر الاتصال
```

**التحقق:**
```bash
# في المشروع
php artisan tinker
>>> DB::getPdo()
# إذا كانت النتيجة: PDO instance، فالاتصال صحيح
```

---

### ❌ خطأ: "SQLSTATE[HY000] [1045] Access denied for user"

**السبب:** كلمة المرور أو المستخدم خاطئ

**الحل:**
```bash
# 1. تحقق من .env
DB_USERNAME=root
DB_PASSWORD=your_password

# 2. اختبر على MySQL مباشرة
mysql -u root -p your_password

# 3. أعد تعيين كلمة المرور إن لزم
# Windows:
mysqld --skip-grant-tables

# 3. إعادة تشغيل MySQL
net stop MySQL80
net start MySQL80
```

---

### ❌ خطأ: "SQLSTATE[HY000] [1049] Unknown database"

**السبب:** قاعدة البيانات غير موجودة

**الحل:**
```bash
# 1. أنشئ قاعدة البيانات
mysql -u root -p
CREATE DATABASE maaninvite;

# 2. أو من Laravel
php artisan migrate:fresh --seed

# 3. تحقق من وجودها
SHOW DATABASES;
```

---

### ❌ خطأ: "Column not found" أو "Undefined column"

**السبب:** نسيت تشغيل الـ migrations

**الحل:**
```bash
# شغّل جميع migrations
php artisan migrate

# أو migrations محددة
php artisan migrate --path=database/migrations/2026_03_07_000001_add_subscription_enhancements.php

# تحقق من الـ migrations المشغلة
php artisan migrate:status
```

---

### ❌ خطأ: "Call to undefined method" على Model

**السبب:** نسيت تعريف العلاقة (Relationship)

**الحل:**
```php
// في Model، تأكد من وجود العلاقة
public function company()
{
    return $this->belongsTo(Company::class);
}

// أو إذا كانت موجودة
// تحقق من أسماء الـ foreign keys
public function invitations()
{
    return $this->hasMany(EventInvitation::class, 'event_id', 'id');
}
```

---

## 2️⃣ مشاكل المصادقة (Authentication)

### ❌ خطأ: "CSRF token mismatch"

**السبب:** الـ CSRF token مفقود أو غير صحيح

**الحل:**
```blade
<!-- في كل form -->
@csrf

<!-- أو في AJAX -->
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

**أو:**
```bash
# امسح cache
php artisan cache:clear
php artisan config:clear

# استخدم متصفح خاص (Incognito)
# أو امسح الـ cookies
```

---

### ❌ خطأ: "Undefined variable session"

**السبب:** الجلسة (Session) لم تبدأ

**الحل:**
```php
// في Kernel.php، تأكد من middleware
'web' => [
    \Illuminate\Session\Middleware\StartSession::class,
    // ...
]

// أو في Controller
session(['key' => 'value']);
```

---

### ❌ OTP لا يصل للبريد

**السبب:** البريد غير مشغّل أو الإعدادات خاطئة

**الحل:**
```bash
# 1. شغّل MailHog
mailhog

# 2. تحقق من .env
MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@maaninvite.test

# 3. جرّب إرسال بريد من tinker
php artisan tinker
>> Mail::raw('Test', function($m) { $m->to('test@test.com'); })

# 4. افحص http://localhost:8025
```

---

### ❌ OTP ينتهي بسرعة

**السبب:** الـ expiry time قصير

**الحل:**
```php
// في .env
OTP_EXPIRY_MINUTES=15  # غيّر الرقم

// أو في OnboardingController
$otp->expires_at = now()->addMinutes(config('otp.expiry_minutes', 15));
```

---

## 3️⃣ مشاكل الأحداث والدعوات

### ❌ خطأ: "Event not found"

**السبب:** معرّف الحدث غير صحيح

**الحل:**
```php
// افحص الـ slug أو ID
// في الـ route
Route::get('/events/{event}', function(Event $event) {
    return $event; // implicit route model binding
});

// أو يدويًا
$event = Event::find($id) ?? Event::where('slug', $slug)->first();
```

---

### ❌ CSV import فشل

**السبب:** صيغة CSV خاطئة أو حجم كبير

**الحل:**
```bash
# الصيغة الصحيحة:
name,email
أحمد محمد,ahmed@example.com
فاطمة علي,fatima@example.com

# تحقق من:
# 1. ترميز UTF-8
# 2. فاصل العمود: comma (,)
# 3. الصف الأول: headers
# 4. الحد الأقصى: 10,000 صف

# إذا كان الملف كبيرًا
# شغّل بـ chunks
php artisan queue:work
```

---

### ❌ دعوات لا تصل

**السبب:** البريد غير مشغّل أو الـ queue غير يعمل

**الحل:**
```bash
# 1. تأكد من queue.php في .env
QUEUE_CONNECTION=database

# 2. شغّل queue worker
php artisan queue:work

# 3. افحص queued jobs
php artisan queue:failed-jobs

# 4. أعد محاولة الوظائف الفاشلة
php artisan queue:retry all
```

---

## 4️⃣ مشاكل ماسح QR

### ❌ "Cannot access camera" (لا يمكن الوصول للكاميرا)

**السبب:** الكاميرا محظورة في المتصفح أو غير موجودة

**الحل:**
```bash
# 1. Chrome/Edge/Firefox
# انقر على أيقونة الكاميرا في شريط العناوين
# → اختبر الأذونات

# 2. Windows
Settings → Privacy & Security → Camera
→ تأكد من تفعيل الكاميرا

# 3. اختبر الكاميرا
https://webcamtests.com

# 4. استخدم HTTPS للكاميرا
# (HTTP يعمل فقط في localhost)
```

---

### ❌ "No QR codes detected" (لا يتم الكشف عن QR)

**السبب:** QR code غير واضح أو الإضاءة سيئة

**الحل:**
```bash
# 1. حسّن الإضاءة
# - ضع مصدر ضوء خلف الحدث
# - تجنب الظلال على QR

# 2. QR code واضح
# - استخدم نص بيانات قصير فقط اللازم
# - تجنب الألوان الفاتحة جدًا

# 3. في الكود
// تحقق من جودة QR في قاعدة البيانات
$qr_code = QrCode::format('png')->size(300)->generate($data);
```

---

### ❌ "QR token expired"

**السبب:** الـ QR token انتهت صلاحيته

**الحل:**
```bash
# قم بتجديد الـ QR codes
php artisan tickets:refresh-qr-codes

# أو يدويًا
// في CheckinController
$token = $invitation->qr_token;
if (Carbon::parse($token->expires_at)->isPast()) {
    $token->refresh();
}
```

---

## 5️⃣ مشاكل الاشتراكات

### ❌ "Trial expired" رغم أن الأيام لم تنتهِ

**السبب:** خطأ في تاريخ الانتهاء أو الـ timezone

**الحل:**
```php
// تحقق من الـ timezone في config/app.php
'timezone' => 'Asia/Riyadh',  // صحيح للسعودية

// أو في .env
APP_TIMEZONE=Asia/Riyadh

// تحقق من تاريخ الانتهاء
php artisan tinker
>> $company = Company::find(1)
>> $company->subscription->trial_ends_at
>> now()  // قارن التاريخين
```

---

### ❌ "Plan not found" عند الترقية

**السبب:** الـ plan غير موجودة في قاعدة البيانات

**الحل:**
```bash
# تشغيل الـ seeder
php artisan db:seed --class=SubscriptionPlanSeeder

# تحقق من الخطط
php artisan tinker
>> SubscriptionPlan::all()
```

---

### ❌ دفع الـ Stripe لا يعمل

**السبب:** مفاتيح Stripe غير صحيحة

**الحل:**
```bash
# في .env
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxx

# احصل على المفاتيح من:
# https://dashboard.stripe.com/apikeys

# اختبر التوصيل
php artisan tinker
>> Stripe\Stripe::setApiKey(config('services.stripe.secret'))
>> Stripe\Customer::all()
```

---

## 6️⃣ مشاكل الأداء

### ❌ "The application has encountered an error"

**السبب:** خطأ عام في التطبيق

**الحل:**
```bash
# 1. افحص السجل
tail -f storage/logs/laravel.log

# 2. أو في Windows
Get-Content storage\logs\laravel.log -Tail 50

# 3. فعّل debug mode في .env
APP_DEBUG=true

# 4. امسح cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

### ❌ الموقع بطيء جدًا

**السبب:** استعلامات بطيئة أو عدم استخدام eager loading

**الحل:**
```php
// استخدم eager loading
Event::with('invitations', 'tickets')->get();

// أو
Event::whereHas('invitations')->get();

// تحقق من الاستعلامات
php artisan tinker
>> DB::enableQueryLog()
>> Event::with('invitations')->first()
>> DB::getQueryLog()
```

---

### ❌ الذاكرة امتلأت (Out of Memory)

**السبب:** تحميل كمية كبيرة من البيانات

**الحل:**
```php
// استخدم chunk
EventInvitation::where('event_id', $eventId)
    ->chunk(1000, function($invitations) {
        foreach ($invitations as $invitation) {
            // معالجة
        }
    });

// أو في الـ job
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessInvitations implements ShouldQueue
{
    // يقسم العملية على chunks
}
```

---

## 7️⃣ مشاكل الواجهة (UI)

### ❌ jQuery لا يعمل

**السبب:** jQuery غير محمّل

**الحل:**
```blade
<!-- في layout أو view -->
@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush
```

---

### ❌ Canvas Chart لا يعرض

**السبب:** الـ canvas غير موجود أو Chart.js لم يُحمّل

**الحل:**
```blade
<!-- تأكد من وجود canvas -->
<canvas id="myChart"></canvas>

<!-- حمّل Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ثم الكود-->
<script>
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, { ... });
</script>
```

---

### ❌ HTML5 QR Scanner لا يعمل

**السبب:** html5-qrcode لم يُحمّل أو الكاميرا محظورة

**الحل:**
```blade
<!-- حمّل المكتبة -->
<script src="https://unpkg.com/html5-qrcode@2.2.0/dist/html5-qrcode.min.js"></script>

<!-- تأكد من الأذونات -->
<!-- تحقق من console للأخطاء -->
<!-- افحص https بدل http -->
```

---

## 8️⃣ مشاكل التطوير (Development)

### ❌ Artisan command معطوب

**السبب:** ملف composer.lock أو vendor قديم

**الحل:**
```bash
# أعد تثبيت المتطلبات
composer install

# أو للحالات النادرة
rm -rf vendor
rm composer.lock
composer install
```

---

### ❌ "Class not found"

**السبب:** الـ autoloader لم يُحدّث

**الحل:**
```bash
# قم بتحديث autoloader
composer dump-autoload

# أو with optimization
composer dump-autoload -o
```

---

### ❌ الـ routes لا تعمل

**السبب:** الدعاء من قديم

**الحل:**
```bash
# امسح route cache
php artisan route:clear

# اعرض جميع الـ routes
php artisan route:list

# تحقق من syntax الـ route
php artisan route:list | grep organizer
```

---

## 9️⃣ مشاكل الـ Deployment

### ❌ "Symlink does not exist"

**السبب:** المجلد storage/app/public غير مرتبط

**الحل:**
```bash
# أنشئ symlink
php artisan storage:link

# أو يدويًا
ln -s storage/app/public public/storage  # Linux/Mac
mklink /D public\storage .\storage\app\public  # Windows
```

---

### ❌ ".env في production"

**السبب:** لم نضع .env في الإنتاج

**الحل:**
```bash
# في الخادم
cp .env.example .env

# عدّل القيم
MAIL_DRIVER=smtp
DB_DRIVER=mysql
# ...

# جنّد مفتاح التطبيق
php artisan key:generate

# جرّب
php artisan config:clear
php artisan cache:clear
```

---

### ❌ الأذونات (Permissions) خاطئة

**السبب:** المجلدات لا تملك أذونات الكتابة

**الحل:**
```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data .

# أو
php artisan cache:clear
```

---

## 🔟 Debug Mode

### تشغيل Tinker للاختبار التفاعلي

```bash
php artisan tinker

# أمثلة:
>> Company::count()
>> Event::latest()->first()
>> Mail::send(...)
>> Cache::get('key')
```

### عرض الـ SQL Queries

```php
// في Controller
DB::enableQueryLog();

// منطق العملية

dump(DB::getQueryLog());
```

### استخدام dd() للدينامية

```php
// توقف التنفيذ وعرض قيم
dd($variable);

// أو عرض بدون توقف
dump($variable);
```

---

## 📞 الحصول على المساعدة

1. **افحص السجل:** `storage/logs/laravel.log`
2. **استخدم Tinker:** `php artisan tinker`
3. **شغّل tests:** `php artisan test`
4. **اقرأ الـ docs:** [Laravel Docs](https://laravel.com/docs)

---

**آخر تحديث**: 2026-03-15
**الحالة**: مستمر التحديث
