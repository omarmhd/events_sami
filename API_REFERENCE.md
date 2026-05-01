# API_REFERENCE.md - دليل API الكامل

## 📡 قاعدة الـ API

```
Base URL: http://localhost:8000/api
Authorization: Bearer {token} (عند الحاجة)
Content-Type: application/json
```

---

## 🔐 Authentication Endpoints

### POST /register/send-otp
**إرسال رمز OTP إلى بريد إلكتروني**

**Request:**
```json
{
  "email": "user@example.com"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم إرسال رمز OTP إلى بريدك الإلكتروني",
  "email": "user@example.com",
  "expires_in": 900
}
```

**Response (422):**
```json
{
  "message": "The email field is required",
  "errors": {"email": ["البريد الإلكتروني مطلوب"]}
}
```

---

### POST /register/verify-otp
**التحقق من رمز OTP**

**Request:**
```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم التحقق من الرمز بنجاح",
  "next_step": "profile_setup"
}
```

**Response (422):**
```json
{
  "message": "رمز OTP غير صحيح أو انتهت صلاحيته"
}
```

---

### GET /register/profile-setup
**شاشة إعداد الملف الشخصي**

**Response (200):** HTML Form

---

### POST /register/profile-setup
**حفظ بيانات الملف الشخصي**

**Request:**
```json
{
  "name": "أحمد محمد",
  "phone": "0501234567",
  "company_name": "شركتي الرائدة",
  "subdomain": "my-company"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم إنشاء حسابك بنجاح",
  "company": {
    "id": 1,
    "name": "شركتي الرائدة",
    "subdomain": "my-company",
    "trial_ends_at": "2026-03-30"
  }
}
```

---

## 📋 Events Endpoints

### GET /organizer/events
**قائمة جميع الأحداث**

**Query Parameters:**
```
?page=1
&per_page=15
&sort=-created_at
&filter[type]=private
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "حفل التخريج 2026",
      "description": "...",
      "type": "private",
      "datetime": "2026-04-15T18:00:00",
      "location": "فندق الريتز",
      "capacity": 200,
      "invitations_count": 150,
      "accepted_count": 120,
      "status": "pending"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

---

### POST /organizer/events
**إنشاء حدث جديد**

**Request:**
```json
{
  "name": "حفل التخريج 2026",
  "description": "احتفالية تخريج الدفعة 2026",
  "type": "private",
  "registration_mode": "manual",
  "datetime": "2026-04-15T18:00:00",
  "location": "فندق الريتز",
  "capacity": 200,
  "color": "#6366f1"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "تم إنشاء الحدث بنجاح",
  "event": {
    "id": 1,
    "name": "حفل التخريج 2026",
    "slug": "graduation-ceremony-2026"
  }
}
```

---

### GET /organizer/events/{id}
**تفاصيل حدث واحد**

**Response:**
```json
{
  "id": 1,
  "name": "حفل التخريج 2026",
  "description": "...",
  "type": "private",
  "datetime": "2026-04-15T18:00:00",
  "location": "فندق الريتز",
  "capacity": 200,
  "stats": {
    "total_invitations": 150,
    "accepted": 120,
    "rejected": 10,
    "pending": 20,
    "attended": 110
  }
}
```

---

### PUT /organizer/events/{id}
**تحديث حدث**

**Request:**
```json
{
  "name": "حفل التخريج 2026 (محدث)",
  "capacity": 250
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم تحديث الحدث بنجاح"
}
```

---

### DELETE /organizer/events/{id}
**حذف حدث**

**Response (200):**
```json
{
  "success": true,
  "message": "تم حذف الحدث بنجاح"
}
```

---

## 👥 Invitations Endpoints

### GET /organizer/invitations/{eventId}
**قائمة الدعوات لحدث**

**Query Parameters:**
```
?page=1
&status=pending
&search=ahmed
```

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "invited_name": "أحمد محمد",
      "invited_email": "ahmed@example.com",
      "status": "pending",
      "invited_at": "2026-03-10T10:30:00",
      "responded_at": null,
      "qr_token": "abc123def456"
    }
  ]
}
```

---

### POST /organizer/invitations/{eventId}
**إضافة دعوة يدوية**

**Request:**
```json
{
  "invited_name": "أحمد محمد",
  "invited_email": "ahmed@example.com"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "تم إرسال الدعوة بنجاح"
}
```

---

### POST /organizer/invitations/import
**استيراد دعوات من CSV**

**Request (multipart/form-data):**
```
file: [CSV file]
event_id: 1
```

**CSV Format:**
```
name,email
أحمد محمد,ahmed@example.com
فاطمة علي,fatima@example.com
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم استيراد 250 دعوة بنجاح",
  "imported_count": 250,
  "failed_count": 0
}
```

---

### POST /organizer/invitations/{invitationId}/resend
**إعادة إرسال دعوة**

**Response (200):**
```json
{
  "success": true,
  "message": "تم إعادة إرسال الدعوة"
}
```

---

### POST /organizer/invitations/bulk-resend
**إعادة إرسال دعوات متعددة**

**Request:**
```json
{
  "invitation_ids": [1, 2, 3, 4, 5],
  "event_id": 1
}
```

**Response:**
```json
{
  "success": true,
  "message": "تم إعادة إرسال 5 دعوات",
  "sent_count": 5
}
```

---

### GET /organizer/invitations/{eventId}/export
**تصدير الدعوات إلى CSV**

**Query Parameters:**
```
?format=csv
&include=status
```

**Response:** CSV File

---

### POST /organizer/invitations/{invitationId}/copy-link
**نسخ رابط المدعو**

**Response:**
```json
{
  "success": true,
  "link": "https://maaninvite.test/invite/abc123def456",
  "qr_code_url": "https://maaninvite.test/qr/abc123def456.png"
}
```

---

## 🔍 Check-in Endpoints

### GET /organizer/checkin/{eventSlug}
**شاشة ماسح QR**

**Response:** HTML Form with QR Scanner

---

### POST /organizer/checkin/process
**معالجة مسح QR**

**Request:**
```json
{
  "qr_token": "abc123def456",
  "event_id": 1
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم تسجيل الحضور بنجاح",
  "guest": {
    "name": "أحمد محمد",
    "email": "ahmed@example.com",
    "status": "checked_in"
  }
}
```

**Response (422):**
```json
{
  "success": false,
  "message": "هذا الضيف سبق أن دخل الحفل",
  "code": "already_checked_in"
}
```

---

### GET /organizer/checkin/stats
**الإحصائيات المباشرة**

**Response:**
```json
{
  "total_invitations": 150,
  "checked_in_count": 110,
  "pending_count": 40,
  "checkin_percentage": "73.33%",
  "last_checkin": {
    "name": "أحمد محمد",
    "checked_in_at": "2026-03-15T18:45:00"
  }
}
```

---

### GET /organizer/checkin/recent
**آخر الدخول الحديثة**

**Response:**
```json
[
  {
    "id": 1,
    "guest_name": "أحمد محمد",
    "checked_in_at": "2026-03-15T18:45:00"
  },
  {
    "id": 2,
    "guest_name": "فاطمة علي",
    "checked_in_at": "2026-03-15T18:42:00"
  }
]
```

---

## 📊 Analytics Endpoints

### GET /analytics/company
**لوحة إحصائيات الشركة**

**Response:**
```json
{
  "stats": {
    "total_events": 15,
    "upcoming_events": 3,
    "total_invitations": 2400,
    "acceptance_rate": "80.5%"
  },
  "events": [
    {
      "name": "حفل التخريج",
      "invitations": 200,
      "attended": 160
    }
  ]
}
```

---

### GET /analytics/events/{eventId}
**إحصائيات حدث واحد**

**Response:**
```json
{
  "event": {
    "id": 1,
    "name": "حفل التخريج 2026"
  },
  "stats": {
    "total_invitations": 200,
    "accepted": 160,
    "rejected": 20,
    "pending": 20,
    "attendance_rate": "80%"
  }
}
```

---

### GET /analytics/attendance-report
**تقرير الحضور**

**Query Parameters:**
```
?event_id=1&format=json
```

**Response:**
```json
{
  "event": "حفل التخريج 2026",
  "attendees": [
    {
      "name": "أحمد محمد",
      "email": "ahmed@example.com",
      "status": "attended",
      "checked_in_at": "2026-03-15T18:45:00"
    }
  ]
}
```

---

### GET /analytics/export
**تصدير التقارير**

**Query Parameters:**
```
?event_id=1&format=csv
```

**Response:** CSV File

---

## 💳 Subscription Endpoints

### GET /organizer/subscription
**عرض الاشتراك الحالي**

**Response:**
```json
{
  "subscription": {
    "id": 1,
    "plan_name": "Professional",
    "status": "active",
    "started_at": "2026-01-15",
    "ends_at": "2027-01-15",
    "price": 299
  },
  "usage": {
    "events_used": 45,
    "events_limit": 100,
    "invites_used": 7500,
    "invites_limit": 10000
  },
  "trial": null
}
```

---

### GET /organizer/subscription/upgrade
**صفحة الترقية**

**Response:**
```json
{
  "current_plan": "Starter",
  "plans": [
    {
      "code": "starter",
      "name": "Starter",
      "price": 99,
      "features": [...]
    },
    {
      "code": "professional",
      "name": "Professional",
      "price": 299,
      "features": [...]
    }
  ]
}
```

---

### POST /organizer/subscription/upgrade
**معالجة الترقية**

**Request:**
```json
{
  "plan_code": "professional"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم الترقية بنجاح",
  "invoice_number": "INV-001234"
}
```

---

### POST /organizer/subscription/payment
**معالجة الدفع**

**Request:**
```json
{
  "plan_code": "professional",
  "payment_method": "stripe",
  "token": "tok_visa"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "تم معالجة الدفع بنجاح",
  "subscription": {...}
}
```

---

### GET /organizer/subscription/paywall
**صفحة المقفل**

**Query Parameters:**
```
?reason=event_limit
```

**Response:** HTML with reason-based messaging

---

### GET /organizer/subscription/usage
**الاستخدام الحالي**

**Response:**
```json
{
  "trial": {
    "is_active": false,
    "days_remaining": 0
  },
  "events": {
    "used": 45,
    "limit": 100,
    "percentage": 45
  },
  "invites": {
    "used": 7500,
    "limit": 10000,
    "percentage": 75
  }
}
```

---

## 🔄 Webhook Events

### Event: `subscription.expired`

```json
{
  "event": "subscription.expired",
  "company_id": 1,
  "subscription_id": 1,
  "timestamp": "2026-03-16T10:30:00"
}
```

### Event: `event.created`

```json
{
  "event": "event.created",
  "event_id": 1,
  "event_name": "حفل التخريج",
  "timestamp": "2026-03-16T10:30:00"
}
```

### Event: `invitation.accepted`

```json
{
  "event": "invitation.accepted",
  "invitation_id": 1,
  "guest_name": "أحمد محمد",
  "timestamp": "2026-03-16T10:30:00"
}
```

---

## ⚠️ Error Codes

| Code | Message | الوصف |
|------|---------|--------|
| 200 | OK | نجح الطلب |
| 201 | Created | تم إنشاء مورد جديد |
| 400 | Bad Request | طلب غير صحيح |
| 401 | Unauthorized | غير مصرح |
| 403 | Forbidden | محظور |
| 404 | Not Found | لم يتم العثور |
| 422 | Validation Error | خطأ التحقق |
| 429 | Too Many Requests | طلبات كثيرة |
| 500 | Server Error | خطأ السيرفر |

---

## 🔒 Authentication Headers

```
Authorization: Bearer {YOUR_TOKEN}
Accept: application/json
Content-Type: application/json
X-CSRF-Token: {TOKEN}
```

---

## 📝 Rate Limiting

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1363896378
```

---

**آخر تحديث**: 2026-03-15
**الإصدار**: 1.0.0
