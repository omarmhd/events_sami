# QUICK_START.md - البدء السريع في 10 دقائق

## ⚡ خطوات البدء السريع

### 1️⃣ التثبيت (٣ دقائق)

```bash
# 1. الدخول للمجلد
cd d:\2026\sami-events\ -\ Copy

# 2. تثبيت المتطلبات
composer install
npm install

# 3. نسخ الإعدادات
copy .env.local .env

# 4. توليد المفتاح
php artisan key:generate
```

---

### 2️⃣ قاعدة البيانات (٣ دقائق)

```bash
# 1. إنشاء قاعدة البيانات
# استخدم PHPMyAdmin أو:
mysql -u root -p
CREATE DATABASE maaninvite;
EXIT;

# 2. Migrations و Seeds
php artisan migrate
php artisan db:seed
```

---

### 3️⃣ البدء (٢ دقيقة)

**Terminal 1 - Serve:**
```bash
php artisan serve
# متاح على: http://localhost:8000
```

**Terminal 2 - Mail (اختياري):**
```bash
mailhog
# واجهة الويب: http://localhost:8025
```

**Terminal 3 - Queue:**
```bash
php artisan queue:work
```

---

### 4️⃣ التسجيل (٢ دقيقة)

1. اذهب إلى: http://localhost:8000/register
2. أدخل بريدك
3. افحص البريد (MailHog على port 8025)
4. أدخل الرمز
5. أكمل الملف الشخصي
6. ✅ تم!

---

## 🎯 المسارات الأساسية

```
📲 التسجيل:      http://localhost:8000/register
🏢 اللوحة:        http://localhost:8000/organizer
⚙️ النظام:        http://localhost:8000/admin
📊 الإحصائيات:   http://localhost:8000/analytics
💳 الاشتراك:     http://localhost:8000/subscription
📧 البريد:        http://localhost:8025
```

---

## 🧪 اختبار سريع

### ✅ تسجيل حساب جديد
```
Email: test@maaninvite.test
OTP: من البريد (MailHog)
Name: أحمد محمد
Company: شركتي
Subdomain: my-company
```

### ✅ إنشاء فعالية
```
من لوحة التحكم → الأحداث
اسم + مكان + تاريخ + نوع
حفظ
```

### ✅ إضافة دعوات
```
من الفعالية → إدارة الدعوات
أضف يدويًا أو استورد CSV
انظر البريد المرسل
```

### ✅ ماسح QR
```
من الفعالية → الدخول
امسح رمز من البريد
شاهد الإحصائيات
```

---

## 📋 البيانات الأولية

**الخطط المتاحة بعد الـ seed:**

| الخطة | السعر | الأحداث | الدعوات/الحدث |
|------|------|--------|---------------|
| Starter | $99 | 12 | 100 |
| Professional | $299 | 100 | 1,000 |
| Enterprise | Custom | ∞ | ∞ |

---

## 🆘 مشاكل سريعة

| المشكلة | الحل |
|--------|-----|
| ❌ Database error | `php artisan migrate` |
| ❌ Mail not sent | شغّل `mailhog` |
| ❌ Queue issues | شغّل `php artisan queue:work` |
| ❌ Routes not found | `php artisan route:clear` |
| ❌ 404 Page | تحقق من `.env` URL |

---

## 📚 المستندات الكاملة

- 📖 [SETUP_AND_RUN.md](SETUP_AND_RUN.md) - الإعداد الكامل
- 🧪 [QA_TESTING_CHECKLIST.md](QA_TESTING_CHECKLIST.md) - قائمة الاختبار
- 🔧 [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - حل المشاكل
- 📡 [API_REFERENCE.md](API_REFERENCE.md) - مرجع API
- 📘 [IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md) - الدليل الكامل

---

**Ready? Let's Go! 🚀**

```bash
php artisan serve
# ثم اذهب إلى http://localhost:8000/register
```

