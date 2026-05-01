# DEVELOPMENT_TIPS.md - نصائح التطوير والأفضليات

> **نصائح عملية وأفضليات لتطوير Maan Invite بكفاءة**

---

## 🎯 أفضليات الكود (Best Practices)

### 1️⃣ استخدام التعليقات Arabic-First

```php
// ✅ صحيح - تعليق عربي واضح
// يتحقق من وجود الفعالية قبل الإنشاء
$event = Event::where('slug', $slug)->first();

// ❌ خاطئ - تعليق إنجليزي غير واضح في كود عربي
// Check event existence
$event = Event::where('slug', $slug)->first();
```

---

### 2️⃣ رسائل الأخطاء بالعربية

```php
// ✅ صحيح
throw new ValidationException("لم يتم العثور على الفعالية");

// ❌ خاطئ
throw new ValidationException("Event not found");
```

---

### 3️⃣ استخدام Features Flag للميزات الجديدة

```php
// ✅ صحيح - تفعيل/تعطيل آمن
if (config('features.new_analytics_enabled')) {
    // ميزة جديدة
}

// في config/features.php
'new_analytics_enabled' => env('FEATURES_NEW_ANALYTICS', false)
```

---

### 4️⃣ Eager Loading لتقليل Queries

```php
// ❌ خاطئ - N+1 Problem
$events = Event::all();
foreach ($events as $event) {
    echo $event->user->name; // Query إضافية كل مرة
}

// ✅ صحيح
$events = Event::with('user')->get();
foreach ($events as $event) {
    echo $event->user->name; // No additional queries
}
```

---

### 5️⃣ استخدام Jobs للعمليات الثقيلة

```php
// ❌ خاطئ - يحجز الـ request
class EventController
{
    public function send_invitations()
    {
        foreach ($invitations as $invitation) {
            Mail::send(new InvitationMail($invitation));
        }
    }
}

// ✅ صحيح - غير متزامن
class EventController
{
    public function send_invitations()
    {
        foreach ($invitations as $invitation) {
            dispatch(new SendInvitationEmail($invitation));
        }
    }
}
```

---

### 6️⃣ Validation Messages محصصة

```php
// ✅ في Form Request
public function messages()
{
    return [
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.email' => 'البريد الإلكتروني غير صحيح',
        'event_id.exists' => 'الفعالية غير موجودة',
    ];
}

public function authorize()
{
    return auth()->check() && auth()->user()->owns_company();
}
```

---

### 7️⃣ استخدام Scopes في Models

```php
// ✅ صحيح
$active = Event::active()->where('company_id', $companyId)->get();

// في Model
public function scopeActive($query)
{
    return $query->where('status', 'active')
                 ->whereDate('datetime', '>', now());
}

// ❌ خاطئ
$active = Event::where('status', 'active')
               ->where('company_id', $companyId)
               ->whereDate('datetime', '>', now())
               ->get();
```

---

### 8️⃣ قاعدة التسمية (Naming Convention)

#### Models
```php
Event              // Singular, PascalCase
EventInvitation    // Compound, PascalCase
Ticket             // Singular
```

#### Controllers
```php
EventController           // Singular, PascalCase
EventInvitationController // Compound, PascalCase
```

#### Routes
```php
/events              // plural
/events/{id}         // with ID
/invitations/{id}    // compound plural
```

#### Database Tables
```
events              // plural, snake_case
event_invitations   // compound, snake_case
tickets             // plural
```

#### Methods
```php
getEventStats()     // camelCase
calculateProrated() // camelCase
isTrialExpired()    // camelCase with is/has prefix
```

---

## 🔒 أمان الكود

### 1️⃣ منع SQL Injection

```php
// ❌ خاطئ - Vulnerable
$events = DB::select("SELECT * FROM events WHERE id = " . $id);

// ✅ صحيح
$events = Event::where('id', $id)->get();
```

---

### 2️⃣ منع XSS

```blade
<!-- ❌ خاطئ -->
{!! $user->name !!}

<!-- ✅ صحيح -->
{{ $user->name }}
```

---

### 3️⃣ CSRF Protection

```blade
<!-- تأكد من وجود في جميع النماذج -->
@csrf

<!-- أو في AJAX -->
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

---

### 4️⃣ Authorization

```php
// ✅ صحيح
public function update(Event $event)
{
    $this->authorize('update', $event);
    // ...
}

// أو
if (! auth()->user()->owns($event)) {
    abort(403);
}
```

---

## 🚀 تحسينات الأداء

### 1️⃣ استخدام Caching

```php
// ✅ صحيح
$plans = Cache::remember('subscription_plans', 3600, function () {
    return SubscriptionPlan::all();
});

// لإعادة تحميل الـ cache
Cache::forget('subscription_plans');
```

---

### 2️⃣ Database Indexing

```php
// في Migration
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique()->index();
    $table->foreignId('company_id')->index();
    $table->datetime('datetime')->index();
    $table->timestamps();
});
```

---

### 3️⃣ Pagination

```php
// ✅ صحيح
$events = Event::where('company_id', $companyId)
    ->paginate(15);

// ❌ خاطئ - Load all data
$events = Event::all();
```

---

### 4️⃣ لازي لوديج (Lazy Loading) للعلاقات

```php
// ✅ صحيح - Eager Load
$events = Event::with('invitations', 'tickets')->get();

// ❌ خاطئ
$events = Event::all();
foreach ($events as $event) {
    count($event->invitations);  // Extra query!
}
```

---

## 🧪 اختبار أثناء التطوير

### Running Tests

```bash
# اختبارات الوحدة
php artisan test

# اختبار ملف معين
php artisan test tests/Unit/Services/SubscriptionServiceTest.php

# مع coverage
php artisan test --coverage

# اختبار محدد
php artisan test --filter=OtpValidation
```

---

### مثال على Test

```php
// tests/Feature/OtpAuthTest.php
class OtpAuthTest extends TestCase
{
    public function test_can_send_otp()
    {
        $response = $this->post('/register/send-otp', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('otp_verifications', [
            'email' => 'test@example.com'
        ]);
    }
}
```

---

## 🐛 Debug Tips

### Tinker REPL

```bash
php artisan tinker

# أمثلة
>> Company::count()
>> $company = Company::first()
>> $company->subscription
>> Mail::send(new TestMail())
>> Cache::get('key')
```

---

### SQL Query Logging

```php
// في Controller أو Service
DB::enableQueryLog();

// كودك هنا

dump(DB::getQueryLog());
```

---

### Dump Variables

```php
dump($variable);           // عرض الـ variable والمتابعة
dd($variable);            // عرض وتوقف
dd($var1, $var2, $var3);  // عرض متعدد
```

---

## 📝 توثيق الكود

### PHPDoc Comments

```php
/**
 * احسب عدد الأيام المتبقية في التجربة
 * 
 * @return int عدد الأيام المتبقية (صفر إذا انتهت)
 */
public function getTrialDaysRemaining(): int
{
    return max(0, $this->trial_ends_at->diffInDays(now()));
}
```

---

### Blade Comments

```blade
{{-- هذا تعليق محلي لا يظهر في HTML --}}
<div>المحتوى هنا</div>

{{-- قسم: إدارة الدعوات --}}
```

---

## 🔄 Git Workflow

### Branches

```bash
# ميزة جديدة
git checkout -b feature/new-analytics

# إصلاح
git checkout -b fix/otp-validation

# تحسينات
git checkout -b improve/email-templates
```

---

### Commits

```bash
# ✅ صحيح - واضح ومفيد
git commit -m "Add subscription prorated upgrade logic"
git commit -m "Fix QR code validation edge case"
git commit -m "Improve email template styling"

# ❌ خاطئ
git commit -m "fix stuff"
git commit -m "update"
git commit -m "asdf"
```

---

## 🎯 قائمة تحقق قبل الـ Pull Request

- [ ] الكود ية العمل بدون أخطاء
- [ ] استخدام الأفضليات (Best Practices)
- [ ] الرسائل/الأخطاء بالعربية
- [ ] الاختبارات تمر (All tests pass)
- [ ] لا توجد تحذيرات (No warnings)
- [ ] التوثيق محدّث
- [ ] الـ commit message واضح

---

## 🚀 Development Server Tips

### Hot Reload للـ CSS/JS

```bash
npm run dev

# أو
npm run watch
```

---

### Mail Testing مع MailHog

```bash
mailhog

# ثم افحص على http://localhost:8025
```

---

### Queue Testing

```bash
php artisan queue:work

# بـ watch mode (يعيد التحميل)
php artisan queue:work --daemon
```

---

## 📦 الـ Dependencies والـ Versions

### القوائم الموصى بها

**Production:**
```bash
composer install --no-dev
npm ci --production
```

**Development:**
```bash
composer install
npm install
```

---

### تحديث الـ Dependencies

```bash
# اختبر قبل التحديث!
composer update --dry-run

# ثم حقيقي
composer update
```

---

## 💡 نصائح نهائية

1. **اقرأ الكود الموجود قبل الكتابة** - افهم الأسلوب والنمط
2. **استخدم IDE مثل PhpStorm** - للإكمال التلقائي والتحليل
3. **Active مع الفريق** - شارك الأشياء الجديدة
4. **اكتب اختبارات أولاً** - TDD = أفضل كود
5. **الأداء مهم** - استخدم الـ profiling tools
6. **الأمان أساسي** - راجع قبل الـ commit
7. **الفريق أولاً** - الكود المفهوم أفضل من الذكي

---

## 🔗 موارد مفيدة

- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [PHP Standards Recommendations (PSR)](https://www.php-fig.org/)
- [OWASP Security Guide](https://owasp.org/)
- [12 Factor App](https://12factor.net/)

---

**آخر تحديث**: 2026-03-15
**الحالة**: ✨ معايير عالية
