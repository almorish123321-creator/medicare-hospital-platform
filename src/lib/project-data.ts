// MediCare Pro - Complete Project Data

export interface ApiEndpoint {
  method: string;
  path: string;
  description_ar: string;
  description_en: string;
  auth: boolean;
  roles: string[];
  params?: { name: string; type: string; required: boolean; description: string }[];
  body?: { name: string; type: string; required: boolean; description: string }[];
  response?: string;
}

export interface DbTable {
  name: string;
  name_ar: string;
  columns: { name: string; type: string; nullable: boolean; description_ar: string; description_en: string }[];
  relations?: string;
}

export const projectInfo = {
  name: "MediCare Pro",
  name_ar: "ميدي كير برو",
  subtitle: "Integrated Hospital & Clinic Management System",
  subtitle_ar: "نظام متكامل لإدارة المستشفيات والعيادات",
  version: "1.0.0",
  laravel: "11.x",
  php: "8.3",
  license: "MIT",
  description_ar: "نظام إدارة مستشفيات متكامل يدعم اللغة العربية والإنجليزية مع 7 أدوار مستخدمين، نظام طوابير ذكي، إشعارات فورية، دفع إلكتروني، وتقارير متقدمة.",
  description_en: "Comprehensive hospital management system with Arabic/English support, 7 user roles, smart queue system, real-time notifications, electronic payments, and advanced reports.",
  techStack: [
    { name: "Laravel 11", icon: "🇱🇦", desc: "PHP Framework" },
    { name: "PHP 8.3", icon: "🐘", desc: "Backend Language" },
    { name: "MySQL 8", icon: "🐬", desc: "Database" },
    { name: "Redis", icon: "🔴", desc: "Cache & Queue" },
    { name: "Sanctum", icon: "🔐", desc: "Authentication" },
    { name: "Docker", icon: "🐳", desc: "Containerization" },
    { name: "Swagger", icon: "📖", desc: "API Documentation" },
    { name: "Soketi", icon: "⚡", desc: "WebSocket Server" },
    { name: "FCM", icon: "📱", desc: "Push Notifications" },
    { name: "Twilio", icon: "📲", desc: "SMS Service" },
  ],
  roles: [
    { name: "super_admin", name_ar: "المدير العام", color: "#DC2626", icon: "👑" },
    { name: "hospital_admin", name_ar: "مدير المستشفى", color: "#EA580C", icon: "🏥" },
    { name: "doctor", name_ar: "طبيب", color: "#16A34A", icon: "👨‍⚕️" },
    { name: "receptionist", name_ar: "موظف استقبال", color: "#2563EB", icon: "📋" },
    { name: "nurse", name_ar: "ممرض/ة", color: "#9333EA", icon: "👩‍⚕️" },
    { name: "pharmacist", name_ar: "صيدلي", color: "#0891B2", icon: "💊" },
    { name: "patient", name_ar: "مريض", color: "#CA8A04", icon: "🤒" },
  ],
  stats: {
    migrations: 22,
    models: 18,
    controllers: 31,
    services: 6,
    jobs: 9,
    events: 5,
    listeners: 5,
    traits: 3,
    repositories: 12,
    formRequests: 24,
    apiResources: 17,
    middleware: 3,
    seeders: 10,
    factories: 13,
    tests: 15,
    commands: 6,
    languageFiles: 18,
    apiEndpoints: 80,
    totalPhpFiles: 246,
  }
};

export const apiEndpoints: Record<string, ApiEndpoint[]> = {
  public: [
    { method: "GET", path: "/api/v1/hospitals", description_ar: "قائمة المستشفيات", description_en: "List all hospitals", auth: false, roles: [], response: "HospitalResource[]" },
    { method: "GET", path: "/api/v1/hospitals/{hospital}", description_ar: "تفاصيل المستشفى", description_en: "Get hospital details", auth: false, roles: [], params: [{ name: "hospital", type: "integer", required: true, description: "Hospital ID" }], response: "HospitalResource" },
    { method: "GET", path: "/api/v1/hospitals/{hospital}/doctors", description_ar: "أطباء المستشفى", description_en: "List hospital doctors", auth: false, roles: [], params: [{ name: "hospital", type: "integer", required: true, description: "Hospital ID" }], response: "DoctorResource[]" },
    { method: "GET", path: "/api/v1/hospitals/{hospital}/departments", description_ar: "أقسام المستشفى", description_en: "List hospital departments", auth: false, roles: [], params: [{ name: "hospital", type: "integer", required: true, description: "Hospital ID" }], response: "DepartmentResource[]" },
    { method: "GET", path: "/api/v1/doctors/{doctor}", description_ar: "تفاصيل الطبيب", description_en: "Get doctor details", auth: false, roles: [], params: [{ name: "doctor", type: "integer", required: true, description: "Doctor ID" }], response: "DoctorResource" },
    { method: "GET", path: "/api/v1/doctors/{doctor}/reviews", description_ar: "تقييمات الطبيب", description_en: "Get doctor reviews", auth: false, roles: [], params: [{ name: "doctor", type: "integer", required: true, description: "Doctor ID" }], response: "ReviewResource[]" },
    { method: "GET", path: "/api/v1/doctors/{doctor}/schedule", description_ar: "جدول الطبيب", description_en: "Get doctor schedule", auth: false, roles: [], params: [{ name: "doctor", type: "integer", required: true, description: "Doctor ID" }], response: "Schedule" },
    { method: "GET", path: "/api/v1/languages", description_ar: "اللغات المتاحة", description_en: "Available languages", auth: false, roles: [], response: "Language[]" },
  ],
  auth: [
    { method: "POST", path: "/api/v1/auth/register", description_ar: "تسجيل مريض جديد", description_en: "Register new patient", auth: false, roles: [], body: [{ name: "name", type: "string", required: true, description: "Patient full name" }, { name: "email", type: "email", required: true, description: "Email address" }, { name: "phone", type: "string", required: true, description: "Phone number" }, { name: "password", type: "string", required: true, description: "Min 8 chars" }, { name: "hospital_id", type: "integer", required: true, description: "Hospital ID" }] },
    { method: "POST", path: "/api/v1/auth/login", description_ar: "تسجيل الدخول", description_en: "Login user", auth: false, roles: [], body: [{ name: "email", type: "email", required: true, description: "Email address" }, { name: "password", type: "string", required: true, description: "Password" }, { name: "device_token", type: "string", required: false, description: "FCM device token" }] },
    { method: "POST", path: "/api/v1/auth/forgot-password", description_ar: "طلب استعادة كلمة المرور", description_en: "Request password reset", auth: false, roles: [], body: [{ name: "email", type: "email", required: true, description: "Email address" }] },
    { method: "POST", path: "/api/v1/auth/reset-password", description_ar: "إعادة تعيين كلمة المرور", description_en: "Reset password", auth: false, roles: [], body: [{ name: "token", type: "string", required: true, description: "Reset token" }, { name: "email", type: "email", required: true, description: "Email" }, { name: "password", type: "string", required: true, description: "New password" }, { name: "password_confirmation", type: "string", required: true, description: "Confirm password" }] },
    { method: "POST", path: "/api/v1/auth/logout", description_ar: "تسجيل الخروج", description_en: "Logout user", auth: true, roles: ["all"] },
    { method: "POST", path: "/api/v1/auth/refresh", description_ar: "تحديث رمز المصادقة", description_en: "Refresh auth token", auth: true, roles: ["all"] },
    { method: "GET", path: "/api/v1/auth/me", description_ar: "بيانات المستخدم الحالي", description_en: "Current user data", auth: true, roles: ["all"], response: "UserResource" },
    { method: "POST", path: "/api/v1/auth/change-language", description_ar: "تغيير اللغة", description_en: "Change language", auth: true, roles: ["all"], body: [{ name: "language", type: "string", required: true, description: "ar or en" }] },
  ],
  patient: [
    { method: "GET", path: "/api/v1/patient/profile", description_ar: "عرض الملف الشخصي", description_en: "View profile", auth: true, roles: ["patient"], response: "PatientResource" },
    { method: "PUT", path: "/api/v1/patient/profile", description_ar: "تحديث الملف الشخصي", description_en: "Update profile", auth: true, roles: ["patient"], body: [{ name: "name", type: "string", required: false, description: "Full name" }, { name: "phone", type: "string", required: false, description: "Phone" }, { name: "date_of_birth", type: "date", required: false, description: "Date of birth" }, { name: "gender", type: "string", required: false, description: "male/female" }, { name: "blood_type", type: "string", required: false, description: "A+, O-, etc." }] },
    { method: "GET", path: "/api/v1/patient/appointments", description_ar: "قائمة المواعيد", description_en: "List appointments", auth: true, roles: ["patient"], response: "AppointmentResource[]" },
    { method: "POST", path: "/api/v1/patient/appointments", description_ar: "حجز موعد جديد", description_en: "Book appointment", auth: true, roles: ["patient"], body: [{ name: "doctor_id", type: "integer", required: true, description: "Doctor ID" }, { name: "department_id", type: "integer", required: true, description: "Department ID" }, { name: "appointment_date", type: "date", required: true, description: "Date" }, { name: "appointment_time", type: "time", required: true, description: "Time (08:00-20:00)" }, { name: "type", type: "string", required: true, description: "consultation/follow_up" }, { name: "payment_method", type: "string", required: true, description: "cash/card/wallet/insurance" }] },
    { method: "GET", path: "/api/v1/patient/appointments/{appointment}", description_ar: "تفاصيل الموعد", description_en: "Appointment details", auth: true, roles: ["patient"] },
    { method: "DELETE", path: "/api/v1/patient/appointments/{appointment}", description_ar: "إلغاء الموعد", description_en: "Cancel appointment", auth: true, roles: ["patient"], body: [{ name: "reason", type: "string", required: true, description: "Cancellation reason" }] },
    { method: "GET", path: "/api/v1/patient/queue-status", description_ar: "حالة الطابور", description_en: "Queue status", auth: true, roles: ["patient"], response: "QueueStatus" },
    { method: "GET", path: "/api/v1/patient/medical-records", description_ar: "السجلات الطبية", description_en: "Medical records", auth: true, roles: ["patient"] },
    { method: "GET", path: "/api/v1/patient/medical-records/{record}", description_ar: "تفاصيل سجل طبي", description_en: "Medical record details", auth: true, roles: ["patient"] },
    { method: "GET", path: "/api/v1/patient/prescriptions", description_ar: "الوصفات الطبية", description_en: "Prescriptions", auth: true, roles: ["patient"] },
    { method: "GET", path: "/api/v1/patient/prescriptions/{prescription}", description_ar: "تفاصيل وصفة", description_en: "Prescription details", auth: true, roles: ["patient"] },
    { method: "GET", path: "/api/v1/patient/invoices", description_ar: "الفواتير", description_en: "Invoices", auth: true, roles: ["patient"] },
    { method: "POST", path: "/api/v1/patient/invoices/{invoice}/pay", description_ar: "دفع فاتورة", description_en: "Pay invoice", auth: true, roles: ["patient"], body: [{ name: "payment_method", type: "string", required: true, description: "cash/card/wallet/insurance" }, { name: "amount", type: "number", required: true, description: "Amount to pay" }] },
    { method: "GET", path: "/api/v1/patient/notifications", description_ar: "الإشعارات", description_en: "Notifications", auth: true, roles: ["patient"] },
    { method: "PUT", path: "/api/v1/patient/notifications/{notification}/read", description_ar: "تعليم إشعار كمقروء", description_en: "Mark notification as read", auth: true, roles: ["patient"] },
  ],
  doctor: [
    { method: "GET", path: "/api/v1/doctor/dashboard", description_ar: "لوحة تحكم الطبيب", description_en: "Doctor dashboard", auth: true, roles: ["doctor"] },
    { method: "GET", path: "/api/v1/doctor/appointments", description_ar: "قائمة المواعيد", description_en: "List appointments", auth: true, roles: ["doctor"] },
    { method: "GET", path: "/api/v1/doctor/appointments/{appointment}", description_ar: "تفاصيل الموعد", description_en: "Appointment details", auth: true, roles: ["doctor"] },
    { method: "PUT", path: "/api/v1/doctor/appointments/{appointment}/start", description_ar: "بدء الاستشارة", description_en: "Start consultation", auth: true, roles: ["doctor"] },
    { method: "PUT", path: "/api/v1/doctor/appointments/{appointment}/complete", description_ar: "إنهاء الاستشارة", description_en: "Complete consultation", auth: true, roles: ["doctor"] },
    { method: "POST", path: "/api/v1/doctor/medical-records", description_ar: "إنشاء سجل طبي", description_en: "Create medical record", auth: true, roles: ["doctor"], body: [{ name: "patient_id", type: "integer", required: true, description: "Patient ID" }, { name: "appointment_id", type: "integer", required: true, description: "Appointment ID" }, { name: "diagnosis", type: "string", required: true, description: "Diagnosis" }, { name: "symptoms", type: "string", required: true, description: "Symptoms" }, { name: "notes", type: "text", required: false, description: "Additional notes" }, { name: "vital_signs", type: "object", required: false, description: "Temperature, BP, HR, etc." }] },
    { method: "PUT", path: "/api/v1/doctor/medical-records/{record}", description_ar: "تحديث سجل طبي", description_en: "Update medical record", auth: true, roles: ["doctor"] },
    { method: "GET", path: "/api/v1/doctor/prescriptions", description_ar: "الوصفات الطبية", description_en: "List prescriptions", auth: true, roles: ["doctor"] },
    { method: "POST", path: "/api/v1/doctor/prescriptions", description_ar: "إنشاء وصفة طبية", description_en: "Create prescription", auth: true, roles: ["doctor"], body: [{ name: "patient_id", type: "integer", required: true, description: "Patient ID" }, { name: "medical_record_id", type: "integer", required: true, description: "Medical record ID" }, { name: "diagnosis", type: "string", required: true, description: "Diagnosis" }, { name: "items[]", type: "array", required: true, description: "Medication items" }] },
    { method: "GET", path: "/api/v1/doctor/patients", description_ar: "قائمة المرضى", description_en: "List patients", auth: true, roles: ["doctor"] },
    { method: "GET", path: "/api/v1/doctor/schedule", description_ar: "جدول العمل", description_en: "Work schedule", auth: true, roles: ["doctor"] },
    { method: "PUT", path: "/api/v1/doctor/schedule", description_ar: "تحديث الجدول", description_en: "Update schedule", auth: true, roles: ["doctor"] },
    { method: "GET", path: "/api/v1/doctor/reviews", description_ar: "التقييمات", description_en: "Reviews", auth: true, roles: ["doctor"] },
  ],
  receptionist: [
    { method: "GET", path: "/api/v1/receptionist/dashboard", description_ar: "لوحة التحكم", description_en: "Dashboard", auth: true, roles: ["receptionist"] },
    { method: "GET", path: "/api/v1/receptionist/appointments", description_ar: "قائمة المواعيد", description_en: "List appointments", auth: true, roles: ["receptionist"] },
    { method: "PUT", path: "/api/v1/receptionist/appointments/{appointment}/check-in", description_ar: "تسجيل وصول المريض", description_en: "Check in patient", auth: true, roles: ["receptionist"] },
    { method: "PUT", path: "/api/v1/receptionist/appointments/{appointment}/no-show", description_ar: "تسجيل عدم الحضور", description_en: "Mark no-show", auth: true, roles: ["receptionist"] },
    { method: "POST", path: "/api/v1/receptionist/walk-in", description_ar: "تسجيل زيارة بدون موعد", description_en: "Walk-in visit", auth: true, roles: ["receptionist"], body: [{ name: "patient_id", type: "integer", required: true, description: "Patient ID" }, { name: "department_id", type: "integer", required: true, description: "Department ID" }, { name: "doctor_id", type: "integer", required: false, description: "Doctor ID" }] },
    { method: "GET", path: "/api/v1/receptionist/queue", description_ar: "حالة الطابور", description_en: "Queue status", auth: true, roles: ["receptionist"] },
    { method: "POST", path: "/api/v1/receptionist/queue/{id}/call", description_ar: "استدعاء المريض التالي", description_en: "Call next patient", auth: true, roles: ["receptionist"] },
    { method: "GET", path: "/api/v1/receptionist/patients", description_ar: "قائمة المرضى", description_en: "List patients", auth: true, roles: ["receptionist"] },
    { method: "POST", path: "/api/v1/receptionist/patients", description_ar: "تسجيل مريض جديد", description_en: "Register new patient", auth: true, roles: ["receptionist"], body: [{ name: "name", type: "string", required: true, description: "Full name" }, { name: "email", type: "email", required: true, description: "Email" }, { name: "phone", type: "string", required: true, description: "Phone" }, { name: "password", type: "string", required: true, description: "Password" }, { name: "date_of_birth", type: "date", required: false, description: "DOB" }] },
  ],
  nurse: [
    { method: "GET", path: "/api/v1/nurse/appointments", description_ar: "مواعيد القياسات الحيوية", description_en: "Vital sign appointments", auth: true, roles: ["nurse"] },
    { method: "POST", path: "/api/v1/nurse/vital-signs/{appointment}", description_ar: "تسجيل القياسات الحيوية", description_en: "Record vital signs", auth: true, roles: ["nurse"], body: [{ name: "temperature", type: "float", required: false, description: "Temperature °C" }, { name: "blood_pressure_systolic", type: "integer", required: false, description: "Systolic BP" }, { name: "blood_pressure_diastolic", type: "integer", required: false, description: "Diastolic BP" }, { name: "heart_rate", type: "integer", required: false, description: "Heart rate BPM" }, { name: "weight", type: "float", required: false, description: "Weight kg" }, { name: "height", type: "float", required: false, description: "Height cm" }] },
    { method: "GET", path: "/api/v1/nurse/patients/{patient}", description_ar: "بيانات المريض", description_en: "Patient data", auth: true, roles: ["nurse"] },
  ],
  pharmacist: [
    { method: "GET", path: "/api/v1/pharmacist/prescriptions", description_ar: "الوصفات المعلقة", description_en: "Pending prescriptions", auth: true, roles: ["pharmacist"] },
    { method: "GET", path: "/api/v1/pharmacist/prescriptions/{prescription}", description_ar: "تفاصيل الوصفة", description_en: "Prescription details", auth: true, roles: ["pharmacist"] },
    { method: "PUT", path: "/api/v1/pharmacist/prescriptions/{prescription}/dispense", description_ar: "صرف الوصفة", description_en: "Dispense prescription", auth: true, roles: ["pharmacist"], body: [{ name: "dispensed_items[]", type: "array", required: true, description: "Items with quantity" }] },
    { method: "GET", path: "/api/v1/pharmacist/medications", description_ar: "قائمة الأدوية", description_en: "Medications list", auth: true, roles: ["pharmacist"] },
    { method: "POST", path: "/api/v1/pharmacist/medications", description_ar: "إضافة دواء", description_en: "Add medication", auth: true, roles: ["pharmacist"] },
    { method: "PUT", path: "/api/v1/pharmacist/medications/{medication}", description_ar: "تحديث دواء", description_en: "Update medication", auth: true, roles: ["pharmacist"] },
    { method: "GET", path: "/api/v1/pharmacist/inventory", description_ar: "حالة المخزون", description_en: "Inventory status", auth: true, roles: ["pharmacist"] },
  ],
  admin: [
    { method: "GET", path: "/api/v1/admin/dashboard", description_ar: "لوحة تحكم المدير", description_en: "Admin dashboard", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/doctors", description_ar: "قائمة الأطباء", description_en: "List doctors", auth: true, roles: ["hospital_admin"] },
    { method: "POST", path: "/api/v1/admin/doctors", description_ar: "إضافة طبيب", description_en: "Add doctor", auth: true, roles: ["hospital_admin"], body: [{ name: "name", type: "string", required: true, description: "Doctor name" }, { name: "email", type: "email", required: true, description: "Email" }, { name: "phone", type: "string", required: true, description: "Phone" }, { name: "specialty", type: "string", required: true, description: "Specialty" }, { name: "department_id", type: "integer", required: true, description: "Department" }, { name: "consultation_fee", type: "number", required: true, description: "Fee amount" }] },
    { method: "PUT", path: "/api/v1/admin/doctors/{doctor}", description_ar: "تحديث طبيب", description_en: "Update doctor", auth: true, roles: ["hospital_admin"] },
    { method: "DELETE", path: "/api/v1/admin/doctors/{doctor}", description_ar: "حذف طبيب", description_en: "Delete doctor", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/departments", description_ar: "الأقسام", description_en: "Departments", auth: true, roles: ["hospital_admin"] },
    { method: "POST", path: "/api/v1/admin/departments", description_ar: "إضافة قسم", description_en: "Add department", auth: true, roles: ["hospital_admin"], body: [{ name: "name_ar", type: "string", required: true, description: "Arabic name" }, { name: "name_en", type: "string", required: true, description: "English name" }] },
    { method: "PUT", path: "/api/v1/admin/departments/{department}", description_ar: "تحديث قسم", description_en: "Update department", auth: true, roles: ["hospital_admin"] },
    { method: "DELETE", path: "/api/v1/admin/departments/{department}", description_ar: "حذف قسم", description_en: "Delete department", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/receptionists", description_ar: "موظفي الاستقبال", description_en: "Receptionists", auth: true, roles: ["hospital_admin"] },
    { method: "POST", path: "/api/v1/admin/receptionists", description_ar: "إضافة موظف استقبال", description_en: "Add receptionist", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/nurses", description_ar: "الممرضين", description_en: "Nurses", auth: true, roles: ["hospital_admin"] },
    { method: "POST", path: "/api/v1/admin/nurses", description_ar: "إضافة ممرض", description_en: "Add nurse", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/pharmacists", description_ar: "الصيادلة", description_en: "Pharmacists", auth: true, roles: ["hospital_admin"] },
    { method: "POST", path: "/api/v1/admin/pharmacists", description_ar: "إضافة صيدلي", description_en: "Add pharmacist", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/reports", description_ar: "التقارير", description_en: "Reports", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/analytics", description_ar: "التحليلات", description_en: "Analytics", auth: true, roles: ["hospital_admin"] },
    { method: "GET", path: "/api/v1/admin/settings", description_ar: "إعدادات المستشفى", description_en: "Hospital settings", auth: true, roles: ["hospital_admin"] },
    { method: "PUT", path: "/api/v1/admin/settings", description_ar: "تحديث الإعدادات", description_en: "Update settings", auth: true, roles: ["hospital_admin"] },
  ],
  superAdmin: [
    { method: "GET", path: "/api/v1/super-admin/hospitals", description_ar: "قائمة المستشفيات", description_en: "List hospitals", auth: true, roles: ["super_admin"] },
    { method: "POST", path: "/api/v1/super-admin/hospitals", description_ar: "إضافة مستشفى", description_en: "Add hospital", auth: true, roles: ["super_admin"], body: [{ name: "name_ar", type: "string", required: true, description: "Arabic name" }, { name: "name_en", type: "string", required: true, description: "English name" }, { name: "email", type: "email", required: true, description: "Email" }, { name: "phone", type: "string", required: true, description: "Phone" }, { name: "subscription_plan_id", type: "integer", required: true, description: "Plan ID" }] },
    { method: "PUT", path: "/api/v1/super-admin/hospitals/{hospital}", description_ar: "تحديث مستشفى", description_en: "Update hospital", auth: true, roles: ["super_admin"] },
    { method: "DELETE", path: "/api/v1/super-admin/hospitals/{hospital}", description_ar: "حذف مستشفى", description_en: "Delete hospital", auth: true, roles: ["super_admin"] },
    { method: "GET", path: "/api/v1/super-admin/plans", description_ar: "خطط الاشتراك", description_en: "Subscription plans", auth: true, roles: ["super_admin"] },
    { method: "POST", path: "/api/v1/super-admin/plans", description_ar: "إضافة خطة اشتراك", description_en: "Add plan", auth: true, roles: ["super_admin"] },
    { method: "PUT", path: "/api/v1/super-admin/plans/{plan}", description_ar: "تحديث خطة", description_en: "Update plan", auth: true, roles: ["super_admin"] },
    { method: "DELETE", path: "/api/v1/super-admin/plans/{plan}", description_ar: "حذف خطة", description_en: "Delete plan", auth: true, roles: ["super_admin"] },
    { method: "GET", path: "/api/v1/super-admin/analytics", description_ar: "تحليلات شاملة", description_en: "Global analytics", auth: true, roles: ["super_admin"] },
    { method: "GET", path: "/api/v1/super-admin/languages", description_ar: "إدارة اللغات", description_en: "Manage languages", auth: true, roles: ["super_admin"] },
    { method: "GET", path: "/api/v1/super-admin/translations", description_ar: "الترجمات", description_en: "Translations", auth: true, roles: ["super_admin"] },
    { method: "POST", path: "/api/v1/super-admin/translations", description_ar: "إضافة ترجمة", description_en: "Add translation", auth: true, roles: ["super_admin"] },
    { method: "PUT", path: "/api/v1/super-admin/translations/{translation}", description_ar: "تحديث ترجمة", description_en: "Update translation", auth: true, roles: ["super_admin"] },
    { method: "PUT", path: "/api/v1/super-admin/default-language", description_ar: "تغيير اللغة الافتراضية", description_en: "Change default language", auth: true, roles: ["super_admin"] },
  ],
};

export const dbTables: DbTable[] = [
  {
    name: "users", name_ar: "المستخدمون",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف الرئيسي", description_en: "Primary key" },
      { name: "name", type: "varchar(255)", nullable: false, description_ar: "الاسم الكامل", description_en: "Full name" },
      { name: "email", type: "varchar(255)", nullable: false, description_ar: "البريد الإلكتروني", description_en: "Email" },
      { name: "phone", type: "varchar(20)", nullable: false, description_ar: "رقم الهاتف", description_en: "Phone number" },
      { name: "password", type: "varchar(255)", nullable: false, description_ar: "كلمة المرور", description_en: "Password (hashed)" },
      { name: "role", type: "enum", nullable: false, description_ar: "الدور (7 أدوار)", description_en: "Role (7 roles)" },
      { name: "hospital_id", type: "foreignId", nullable: true, description_ar: "المستشفى", description_en: "Hospital reference" },
      { name: "department_id", type: "foreignId", nullable: true, description_ar: "القسم", description_en: "Department reference" },
      { name: "language", type: "varchar(5)", nullable: false, description_ar: "اللغة المفضلة", description_en: "Preferred language" },
      { name: "device_token", type: "text", nullable: true, description_ar: "رمز الجهاز FCM", description_en: "FCM device token" },
      { name: "is_active", type: "boolean", nullable: false, description_ar: "مفعل", description_en: "Active status" },
      { name: "timestamps", type: "timestamp", nullable: false, description_ar: "تاريخ الإنشاء والتحديث", description_en: "Created/Updated at" },
      { name: "softDeletes", type: "timestamp", nullable: true, description_ar: "تاريخ الحذف", description_en: "Deleted at" },
    ],
    relations: "hasOne: Doctor, Patient | belongsTo: Hospital, Department"
  },
  {
    name: "hospitals", name_ar: "المستشفيات",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "name_ar", type: "varchar(255)", nullable: false, description_ar: "الاسم بالعربية", description_en: "Arabic name" },
      { name: "name_en", type: "varchar(255)", nullable: false, description_ar: "الاسم بالإنجليزية", description_en: "English name" },
      { name: "email", type: "varchar(255)", nullable: false, description_ar: "البريد الإلكتروني", description_en: "Email" },
      { name: "phone", type: "varchar(20)", nullable: false, description_ar: "الهاتف", description_en: "Phone" },
      { name: "address", type: "text", nullable: true, description_ar: "العنوان", description_en: "Address" },
      { name: "logo", type: "string", nullable: true, description_ar: "الشعار", description_en: "Logo" },
      { name: "subscription_plan_id", type: "foreignId", nullable: false, description_ar: "خطة الاشتراك", description_en: "Subscription plan" },
      { name: "is_active", type: "boolean", nullable: false, description_ar: "مفعل", description_en: "Active status" },
      { name: "subscription_expires_at", type: "timestamp", nullable: true, description_ar: "تاريخ انتهاء الاشتراك", description_en: "Subscription expiry" },
    ],
    relations: "hasMany: Departments, Doctors, Patients, Users | belongsTo: SubscriptionPlan"
  },
  {
    name: "departments", name_ar: "الأقسام",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "name_ar", type: "varchar(255)", nullable: false, description_ar: "الاسم بالعربية", description_en: "Arabic name" },
      { name: "name_en", type: "varchar(255)", nullable: false, description_ar: "الاسم بالإنجليزية", description_en: "English name" },
      { name: "description_ar", type: "text", nullable: true, description_ar: "الوصف بالعربية", description_en: "Arabic description" },
      { name: "description_en", type: "text", nullable: true, description_ar: "الوصف بالإنجليزية", description_en: "English description" },
      { name: "is_active", type: "boolean", nullable: false, description_ar: "مفعل", description_en: "Active" },
    ],
    relations: "hasMany: Doctors, QueueLogs | belongsTo: Hospital"
  },
  {
    name: "doctors", name_ar: "الأطباء",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "user_id", type: "foreignId", nullable: false, description_ar: "المستخدم", description_en: "User" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "department_id", type: "foreignId", nullable: false, description_ar: "القسم", description_en: "Department" },
      { name: "specialty_ar", type: "varchar(255)", nullable: false, description_ar: "التخصص بالعربية", description_en: "Arabic specialty" },
      { name: "specialty_en", type: "varchar(255)", nullable: false, description_ar: "التخصص بالإنجليزية", description_en: "English specialty" },
      { name: "license_number", type: "varchar(100)", nullable: false, description_ar: "رقم الترخيص", description_en: "License number" },
      { name: "bio_ar", type: "text", nullable: true, description_ar: "السيرة الذاتية", description_en: "Bio" },
      { name: "bio_en", type: "text", nullable: true, description_ar: "السيرة الذاتية", description_en: "Bio (English)" },
      { name: "consultation_fee", type: "decimal(10,2)", nullable: false, description_ar: "رسوم الاستشارة", description_en: "Consultation fee" },
      { name: "schedule", type: "json", nullable: true, description_ar: "جدول العمل", description_en: "Work schedule (JSON)" },
    ],
    relations: "belongsTo: User, Hospital, Department | hasMany: Appointments, MedicalRecords, Prescriptions, Reviews"
  },
  {
    name: "patients", name_ar: "المرضى",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "user_id", type: "foreignId", nullable: false, description_ar: "المستخدم", description_en: "User" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "date_of_birth", type: "date", nullable: true, description_ar: "تاريخ الميلاد", description_en: "Date of birth" },
      { name: "gender", type: "enum", nullable: true, description_ar: "الجنس", description_en: "Gender" },
      { name: "blood_type", type: "varchar(5)", nullable: true, description_ar: "فصيلة الدم", description_en: "Blood type" },
      { name: "address", type: "text", nullable: true, description_ar: "العنوان", description_en: "Address" },
      { name: "national_id", type: "varchar(50)", nullable: true, description_ar: "رقم الهوية", description_en: "National ID" },
      { name: "allergies", type: "json", nullable: true, description_ar: "الحساسية", description_en: "Allergies (JSON)" },
      { name: "chronic_diseases", type: "json", nullable: true, description_ar: "الأمراض المزمنة", description_en: "Chronic diseases (JSON)" },
    ],
    relations: "belongsTo: User, Hospital | hasMany: Appointments, MedicalRecords"
  },
  {
    name: "appointments", name_ar: "المواعيد",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "doctor_id", type: "foreignId", nullable: false, description_ar: "الطبيب", description_en: "Doctor" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "department_id", type: "foreignId", nullable: false, description_ar: "القسم", description_en: "Department" },
      { name: "appointment_date", type: "date", nullable: false, description_ar: "التاريخ", description_en: "Date" },
      { name: "appointment_time", type: "time", nullable: false, description_ar: "الوقت", description_en: "Time" },
      { name: "type", type: "enum", nullable: false, description_ar: "النوع", description_en: "Type (consultation/follow_up)" },
      { name: "status", type: "enum", nullable: false, description_ar: "الحالة", description_en: "Status (10 states)" },
      { name: "queue_number", type: "string", nullable: true, description_ar: "رقم الطابور", description_en: "Queue number" },
      { name: "notes", type: "text", nullable: true, description_ar: "ملاحظات", description_en: "Notes" },
      { name: "cancellation_reason", type: "text", nullable: true, description_ar: "سبب الإلغاء", description_en: "Cancellation reason" },
    ],
    relations: "belongsTo: Patient, Doctor, Hospital, Department | hasOne: MedicalRecord"
  },
  {
    name: "queue_logs", name_ar: "سجلات الطوابير",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "department_id", type: "foreignId", nullable: false, description_ar: "القسم", description_en: "Department" },
      { name: "appointment_id", type: "foreignId", nullable: false, description_ar: "الموعد", description_en: "Appointment" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "queue_number", type: "string", nullable: false, description_ar: "رقم الطابور", description_en: "Queue number (D{id}-{seq})" },
      { name: "status", type: "enum", nullable: false, description_ar: "الحالة", description_en: "Status" },
      { name: "called_at", type: "timestamp", nullable: true, description_ar: "وقت الاستدعاء", description_en: "Called at" },
      { name: "served_at", type: "timestamp", nullable: true, description_ar: "وقت الخدمة", description_en: "Served at" },
    ],
    relations: "belongsTo: Hospital, Department, Appointment, Patient"
  },
  {
    name: "medical_records", name_ar: "السجلات الطبية",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "doctor_id", type: "foreignId", nullable: false, description_ar: "الطبيب", description_en: "Doctor" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "appointment_id", type: "foreignId", nullable: false, description_ar: "الموعد", description_en: "Appointment" },
      { name: "diagnosis_ar", type: "text", nullable: false, description_ar: "التشخيص", description_en: "Diagnosis (Arabic)" },
      { name: "diagnosis_en", type: "text", nullable: false, description_ar: "التشخيص", description_en: "Diagnosis (English)" },
      { name: "symptoms", type: "text", nullable: true, description_ar: "الأعراض", description_en: "Symptoms" },
      { name: "notes", type: "text", nullable: true, description_ar: "ملاحظات", description_en: "Notes" },
      { name: "vital_signs", type: "json", nullable: true, description_ar: "القياسات الحيوية", description_en: "Vital signs (JSON)" },
    ],
    relations: "belongsTo: Patient, Doctor, Hospital, Appointment | hasMany: Prescriptions"
  },
  {
    name: "prescriptions", name_ar: "الوصفات الطبية",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "doctor_id", type: "foreignId", nullable: false, description_ar: "الطبيب", description_en: "Doctor" },
      { name: "medical_record_id", type: "foreignId", nullable: false, description_ar: "السجل الطبي", description_en: "Medical record" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "diagnosis_ar", type: "text", nullable: true, description_ar: "التشخيص", description_en: "Diagnosis" },
      { name: "diagnosis_en", type: "text", nullable: true, description_ar: "التشخيص", description_en: "Diagnosis" },
      { name: "status", type: "enum", nullable: false, description_ar: "الحالة", description_en: "Status" },
      { name: "dispensed_at", type: "timestamp", nullable: true, description_ar: "وقت الصرف", description_en: "Dispensed at" },
      { name: "dispensed_by", type: "foreignId", nullable: true, description_ar: "الصيدلي", description_en: "Pharmacist" },
    ],
    relations: "belongsTo: Patient, Doctor, MedicalRecord, Hospital | hasMany: PrescriptionItems"
  },
  {
    name: "prescription_items", name_ar: "عناصر الوصفة",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "prescription_id", type: "foreignId", nullable: false, description_ar: "الوصفة", description_en: "Prescription" },
      { name: "medication_id", type: "foreignId", nullable: false, description_ar: "الدواء", description_en: "Medication" },
      { name: "dosage", type: "varchar(100)", nullable: false, description_ar: "الجرعة", description_en: "Dosage" },
      { name: "frequency", type: "varchar(50)", nullable: false, description_ar: "التكرار", description_en: "Frequency" },
      { name: "duration", type: "varchar(50)", nullable: false, description_ar: "المدة", description_en: "Duration" },
      { name: "instructions", type: "text", nullable: true, description_ar: "التعليمات", description_en: "Instructions" },
      { name: "quantity_dispensed", type: "integer", nullable: true, description_ar: "الكمية المصروفة", description_en: "Quantity dispensed" },
    ],
    relations: "belongsTo: Prescription, Medication"
  },
  {
    name: "medications", name_ar: "الأدوية",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "name_ar", type: "varchar(255)", nullable: false, description_ar: "الاسم بالعربية", description_en: "Arabic name" },
      { name: "name_en", type: "varchar(255)", nullable: false, description_ar: "الاسم بالإنجليزية", description_en: "English name" },
      { name: "description_ar", type: "text", nullable: true, description_ar: "الوصف", description_en: "Description" },
      { name: "category", type: "varchar(100)", nullable: true, description_ar: "الفئة", description_en: "Category" },
      { name: "dosage_form", type: "varchar(50)", nullable: true, description_ar: "شكل الجرعة", description_en: "Dosage form" },
      { name: "strength", type: "varchar(50)", nullable: true, description_ar: "القوة", description_en: "Strength" },
      { name: "stock_quantity", type: "integer", nullable: false, description_ar: "الكمية في المخزون", description_en: "Stock quantity" },
      { name: "min_stock", type: "integer", nullable: false, description_ar: "الحد الأدنى للمخزون", description_en: "Min stock level" },
      { name: "price", type: "decimal(10,2)", nullable: false, description_ar: "السعر", description_en: "Price" },
      { name: "expiry_date", type: "date", nullable: true, description_ar: "تاريخ الانتهاء", description_en: "Expiry date" },
    ],
    relations: "belongsTo: Hospital | hasMany: PrescriptionItems"
  },
  {
    name: "invoices", name_ar: "الفواتير",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "appointment_id", type: "foreignId", nullable: true, description_ar: "الموعد", description_en: "Appointment" },
      { name: "amount", type: "decimal(10,2)", nullable: false, description_ar: "المبلغ", description_en: "Amount" },
      { name: "tax", type: "decimal(10,2)", nullable: false, description_ar: "الضريبة", description_en: "Tax (15%)" },
      { name: "discount", type: "decimal(10,2)", nullable: false, description_ar: "الخصم", description_en: "Discount" },
      { name: "total", type: "decimal(10,2)", nullable: false, description_ar: "الإجمالي", description_en: "Total" },
      { name: "status", type: "enum", nullable: false, description_ar: "الحالة", description_en: "Status" },
    ],
    relations: "belongsTo: Patient, Hospital, Appointment | hasMany: Payments"
  },
  {
    name: "payments", name_ar: "المدفوعات",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "invoice_id", type: "foreignId", nullable: false, description_ar: "الفاتورة", description_en: "Invoice" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "amount", type: "decimal(10,2)", nullable: false, description_ar: "المبلغ", description_en: "Amount" },
      { name: "method", type: "enum", nullable: false, description_ar: "طريقة الدفع", description_en: "Payment method" },
      { name: "transaction_id", type: "string", nullable: true, description_ar: "رقم المعاملة", description_en: "Transaction ID" },
      { name: "status", type: "enum", nullable: false, description_ar: "الحالة", description_en: "Status" },
    ],
    relations: "belongsTo: Invoice, Patient, Hospital"
  },
  {
    name: "reviews", name_ar: "التقييمات",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "patient_id", type: "foreignId", nullable: false, description_ar: "المريض", description_en: "Patient" },
      { name: "doctor_id", type: "foreignId", nullable: false, description_ar: "الطبيب", description_en: "Doctor" },
      { name: "hospital_id", type: "foreignId", nullable: false, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "rating", type: "tinyint", nullable: false, description_ar: "التقييم (1-5)", description_en: "Rating (1-5)" },
      { name: "comment", type: "text", nullable: true, description_ar: "التعليق", description_en: "Comment" },
    ],
    relations: "belongsTo: Patient, Doctor, Hospital"
  },
  {
    name: "notifications", name_ar: "الإشعارات",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "user_id", type: "foreignId", nullable: false, description_ar: "المستخدم", description_en: "User" },
      { name: "title_ar", type: "varchar(255)", nullable: false, description_ar: "العنوان", description_en: "Title" },
      { name: "title_en", type: "varchar(255)", nullable: false, description_ar: "العنوان", description_en: "Title (English)" },
      { name: "body_ar", type: "text", nullable: true, description_ar: "المحتوى", description_en: "Body" },
      { name: "body_en", type: "text", nullable: true, description_ar: "المحتوى", description_en: "Body (English)" },
      { name: "type", type: "varchar(50)", nullable: true, description_ar: "النوع", description_en: "Type" },
      { name: "data", type: "json", nullable: true, description_ar: "بيانات إضافية", description_en: "Extra data" },
      { name: "is_read", type: "boolean", nullable: false, description_ar: "مقروء", description_en: "Read status" },
    ],
    relations: "belongsTo: User"
  },
  {
    name: "subscription_plans", name_ar: "خطط الاشتراك",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "name_ar", type: "varchar(255)", nullable: false, description_ar: "الاسم بالعربية", description_en: "Arabic name" },
      { name: "name_en", type: "varchar(255)", nullable: false, description_ar: "الاسم بالإنجليزية", description_en: "English name" },
      { name: "price_monthly", type: "decimal(10,2)", nullable: false, description_ar: "السعر الشهري", description_en: "Monthly price" },
      { name: "price_yearly", type: "decimal(10,2)", nullable: false, description_ar: "السعر السنوي", description_en: "Yearly price" },
      { name: "max_doctors", type: "integer", nullable: false, description_ar: "الحد الأقصى للأطباء", description_en: "Max doctors" },
      { name: "max_patients", type: "integer", nullable: false, description_ar: "الحد الأقصى للمرضى", description_en: "Max patients" },
      { name: "max_departments", type: "integer", nullable: false, description_ar: "الحد الأقصى للأقسام", description_en: "Max departments" },
      { name: "features", type: "json", nullable: true, description_ar: "المميزات", description_en: "Features (JSON)" },
      { name: "is_active", type: "boolean", nullable: false, description_ar: "مفعل", description_en: "Active" },
    ],
    relations: "hasMany: Hospitals"
  },
  {
    name: "activity_logs", name_ar: "سجلات النشاط",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "user_id", type: "foreignId", nullable: false, description_ar: "المستخدم", description_en: "User" },
      { name: "hospital_id", type: "foreignId", nullable: true, description_ar: "المستشفى", description_en: "Hospital" },
      { name: "action", type: "varchar(100)", nullable: false, description_ar: "الإجراء", description_en: "Action" },
      { name: "model_type", type: "varchar(255)", nullable: true, description_ar: "نوع النموذج", description_en: "Model type" },
      { name: "model_id", type: "bigint", nullable: true, description_ar: "معرف النموذج", description_en: "Model ID" },
      { name: "properties", type: "json", nullable: true, description_ar: "الخصائص", description_en: "Properties" },
      { name: "ip_address", type: "varchar(45)", nullable: true, description_ar: "عنوان IP", description_en: "IP address" },
    ],
    relations: "belongsTo: User, Hospital"
  },
  {
    name: "translations", name_ar: "الترجمات",
    columns: [
      { name: "id", type: "bigint", nullable: false, description_ar: "المعرف", description_en: "Primary key" },
      { name: "group", type: "varchar(100)", nullable: false, description_ar: "المجموعة", description_en: "Group" },
      { name: "key", type: "varchar(255)", nullable: false, description_ar: "المفتاح", description_en: "Key" },
      { name: "value_ar", type: "text", nullable: true, description_ar: "القيمة بالعربية", description_en: "Arabic value" },
      { name: "value_en", type: "text", nullable: true, description_ar: "القيمة بالإنجليزية", description_en: "English value" },
    ],
    relations: "Standalone translation table"
  },
];

export const setupSteps = [
  { step: 1, title_ar: "تثبيت المتطلبات", title_en: "Install Prerequisites", cmd: "# PHP 8.3+\n# MySQL 8.0+\n# Redis\n# Composer\n# Node.js (for frontend)", details_ar: "تأكد من تثبيت PHP 8.3 و MySQL 8 و Redis و Composer على جهازك", details_en: "Make sure PHP 8.3, MySQL 8, Redis, and Composer are installed" },
  { step: 2, title_ar: "فك ضغط المشروع", title_en: "Extract Project", cmd: "unzip medicare-pro.zip\ncd medicare-pro", details_ar: "فك ضغط ملف ZIP الذي تم تحميله", details_en: "Extract the downloaded ZIP file" },
  { step: 3, title_ar: "تثبيت الحزم", title_en: "Install Packages", cmd: "composer install", details_ar: "تثبيت جميع حزم PHP المطلوبة عبر Composer", details_en: "Install all required PHP packages via Composer" },
  { step: 4, title_ar: "إعداد البيئة", title_en: "Environment Setup", cmd: "cp .env.example .env\nphp artisan key:generate", details_ar: "نسخ ملف البيئة وتوليد مفتاح التشفير", details_en: "Copy environment file and generate encryption key" },
  { step: 5, title_ar: "إعداد قاعدة البيانات", title_en: "Database Setup", cmd: "# Edit .env file with your DB credentials:\nDB_DATABASE=medicare_pro\nDB_USERNAME=root\nDB_PASSWORD=your_password", details_ar: "تعديل ملف .env ببيانات الاتصال بقاعدة البيانات", details_en: "Edit .env file with your database connection credentials" },
  { step: 6, title_ar: "تشغيل المigrations", title_en: "Run Migrations", cmd: "php artisan migrate", details_ar: "إنشاء جميع جداول قاعدة البيانات (18 جدول)", details_en: "Create all database tables (18 tables)" },
  { step: 7, title_ar: "تشغيل الـ Seeders", title_en: "Run Seeders", cmd: "php artisan db:seed", details_ar: "إدخال البيانات التجريبية (مستشفيات، أطباء، مرضى، إلخ)", details_en: "Insert demo data (hospitals, doctors, patients, etc.)" },
  { step: 8, title_ar: "توليد توثيق API", title_en: "Generate API Docs", cmd: "php artisan l5-swagger:generate", details_ar: "توليد توثيق Swagger/OpenAPI", details_en: "Generate Swagger/OpenAPI documentation" },
  { step: 9, title_ar: "تشغيل الخادم", title_en: "Start Server", cmd: "php artisan serve\n\n# Or with Docker:\ndocker-compose up -d --build", details_ar: "تشغيل خادم التطوير على http://localhost:8000", details_en: "Start the development server on http://localhost:8000" },
  { step: 10, title_ar: "اختبار الـ API", title_en: "Test API", cmd: "# API Base URL:\nhttp://localhost:8000/api/v1\n\n# Swagger Docs:\nhttp://localhost:8000/api/documentation", details_ar: "اختبر نقاط النهاية عبر Swagger أو Postman", details_en: "Test endpoints via Swagger or Postman" },
];

export const demoCredentials = [
  { role: "super_admin", name_ar: "المدير العام", email: "super@medicare.com", password: "password" },
  { role: "hospital_admin", name_ar: "مدير المستشفى", email: "admin@hospital1.com", password: "password" },
  { role: "doctor", name_ar: "طبيب", email: "doctor@hospital1.com", password: "password" },
  { role: "receptionist", name_ar: "موظف استقبال", email: "receptionist@hospital1.com", password: "password" },
  { role: "nurse", name_ar: "ممرض", email: "nurse@hospital1.com", password: "password" },
  { role: "pharmacist", name_ar: "صيدلي", email: "pharmacist@hospital1.com", password: "password" },
  { role: "patient", name_ar: "مريض", email: "patient@example.com", password: "password" },
];
