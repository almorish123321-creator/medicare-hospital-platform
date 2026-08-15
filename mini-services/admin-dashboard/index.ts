import { Hono } from 'hono'
import { cors } from 'hono/cors'
import { serveStatic } from 'hono/bun'

const app = new Hono()

app.use('*', cors())

// ═══════════════════════════════════════════════════════════
// ADMIN DASHBOARD API ENDPOINTS
// ═══════════════════════════════════════════════════════════

app.get('/api/dashboard/stats', async (c) => {
  return c.json({
    success: true,
    data: {
      total_patients: 1247,
      total_doctors: 42,
      today_appointments: 89,
      pending_queue: 23,
      total_revenue: 284500,
      monthly_growth: 12.5,
      active_departments: 8,
      bed_occupancy: 73,
      today_completed: 56,
      today_cancelled: 6,
      avg_wait_time: 18,
      patient_satisfaction: 4.6,
    }
  })
})

app.get('/api/dashboard/revenue', async (c) => {
  return c.json({
    success: true,
    data: {
      months: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس'],
      revenue: [185000, 210000, 195000, 245000, 260000, 278000, 290000, 284500],
      expenses: [120000, 135000, 128000, 145000, 155000, 162000, 170000, 168000],
    }
  })
})

app.get('/api/dashboard/appointments-today', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, patient: 'أحمد محمد', doctor: 'د. سارة علي', department: 'بطانة', time: '08:00', status: 'completed', type: 'consultation' },
      { id: 2, patient: 'فاطمة حسن', doctor: 'د. خالد يوسف', department: 'قلب', time: '08:30', status: 'in_progress', type: 'follow_up' },
      { id: 3, patient: 'عمر أحمد', doctor: 'د. سارة علي', department: 'بطانة', time: '09:00', status: 'waiting', type: 'consultation' },
      { id: 4, patient: 'نور الدين', doctor: 'د. منى عبدالله', department: 'أطفال', time: '09:30', status: 'waiting', type: 'consultation' },
      { id: 5, patient: 'سلمى خالد', doctor: 'د. خالد يوسف', department: 'قلب', time: '10:00', status: 'confirmed', type: 'follow_up' },
      { id: 6, patient: 'يوسف إبراهيم', doctor: 'د. محمد رضا', department: 'عظام', time: '10:30', status: 'confirmed', type: 'consultation' },
      { id: 7, patient: 'هدى محمد', doctor: 'د. منى عبدالله', department: 'أطفال', time: '11:00', status: 'pending', type: 'consultation' },
      { id: 8, patient: 'محمود علي', doctor: 'د. سارة علي', department: 'بطانة', time: '11:30', status: 'pending', type: 'follow_up' },
    ]
  })
})

app.get('/api/dashboard/queue', async (c) => {
  return c.json({
    success: true,
    data: {
      department_id: 1,
      department_name: 'القلب والأوعية الدموية',
      current_serving: 'D1-012',
      next_number: 'D1-013',
      waiting_count: 8,
      avg_wait_minutes: 22,
      patients: [
        { queue_number: 'D1-009', patient: 'أحمد محمد', status: 'in_progress', wait_time: 0, priority: false },
        { queue_number: 'D1-010', patient: 'فاطمة حسن', status: 'waiting', wait_time: 15, priority: false },
        { queue_number: 'D1-011', patient: 'عمر أحمد', status: 'waiting', wait_time: 30, priority: true },
        { queue_number: 'D1-012', patient: 'نور الدين', status: 'waiting', wait_time: 45, priority: false },
        { queue_number: 'D1-013', patient: 'سلمى خالد', status: 'waiting', wait_time: 52, priority: false },
      ]
    }
  })
})

app.get('/api/doctors', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, name: 'د. سارة علي', specialty: 'أمراض البطانة', department: 'الباطنة', patients_count: 342, rating: 4.8, status: 'active', schedule: 'السبت - الأربعاء' },
      { id: 2, name: 'د. خالد يوسف', specialty: 'جراحة القلب', department: 'القلب', patients_count: 289, rating: 4.9, status: 'active', schedule: 'الأحد - الخميس' },
      { id: 3, name: 'د. منى عبدالله', specialty: 'طب الأطفال', department: 'الأطفال', patients_count: 198, rating: 4.7, status: 'active', schedule: 'السبت - الخميس' },
      { id: 4, name: 'د. محمد رضا', specialty: 'جراحة العظام', department: 'العظام', patients_count: 156, rating: 4.5, status: 'active', schedule: 'الإثنين - الجمعة' },
      { id: 5, name: 'د. هالة سامي', specialty: 'أمراض جلدية', department: 'الجلدية', patients_count: 267, rating: 4.6, status: 'on_leave', schedule: 'السبت - الأربعاء' },
      { id: 6, name: 'د. عمرو حسين', specialty: 'طب العيون', department: 'العيون', patients_count: 312, rating: 4.4, status: 'active', schedule: 'الأحد - الخميس' },
    ]
  })
})

app.get('/api/departments', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, name_ar: 'القلب والأوعية الدموية', name_en: 'Cardiology', doctors_count: 6, patients_today: 18, status: 'active' },
      { id: 2, name_ar: 'الباطنة', name_en: 'Internal Medicine', doctors_count: 5, patients_today: 24, status: 'active' },
      { id: 3, name_ar: 'طب الأطفال', name_en: 'Pediatrics', doctors_count: 4, patients_today: 15, status: 'active' },
      { id: 4, name_ar: 'جراحة العظام', name_en: 'Orthopedics', doctors_count: 3, patients_today: 12, status: 'active' },
      { id: 5, name_ar: 'الأمراض الجلدية', name_en: 'Dermatology', doctors_count: 3, patients_today: 9, status: 'active' },
      { id: 6, name_ar: 'طب العيون', name_en: 'Ophthalmology', doctors_count: 4, patients_today: 11, status: 'active' },
      { id: 7, name_ar: 'الأنف والأذن والحنجرة', name_en: 'ENT', doctors_count: 3, patients_today: 7, status: 'active' },
      { id: 8, name_ar: 'الأشعة والتشخيص', name_en: 'Radiology', doctors_count: 2, patients_today: 20, status: 'active' },
    ]
  })
})

app.get('/api/patients', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, name: 'أحمد محمد علي', phone: '+966501234567', email: 'ahmed@example.com', last_visit: '2026-08-14', total_visits: 12, blood_type: 'A+', status: 'active' },
      { id: 2, name: 'فاطمة حسن محمود', phone: '+966509876543', email: 'fatma@example.com', last_visit: '2026-08-14', total_visits: 8, blood_type: 'O+', status: 'active' },
      { id: 3, name: 'عمر أحمد سعيد', phone: '+966503456789', email: 'omar@example.com', last_visit: '2026-08-13', total_visits: 5, blood_type: 'B+', status: 'active' },
      { id: 4, name: 'نور الدين محمد', phone: '+966507654321', email: 'nour@example.com', last_visit: '2026-08-12', total_visits: 15, blood_type: 'AB+', status: 'active' },
      { id: 5, name: 'سلمى خالد عبدالله', phone: '+966502345678', email: 'salma@example.com', last_visit: '2026-08-10', total_visits: 3, blood_type: 'O-', status: 'inactive' },
    ]
  })
})

app.get('/api/reports/doctor-performance', async (c) => {
  return c.json({
    success: true,
    data: [
      { doctor: 'د. سارة علي', appointments: 156, completion_rate: 94, avg_rating: 4.8, revenue: 78000 },
      { doctor: 'د. خالد يوسف', appointments: 132, completion_rate: 97, avg_rating: 4.9, revenue: 92400 },
      { doctor: 'د. منى عبدالله', appointments: 98, completion_rate: 91, avg_rating: 4.7, revenue: 49000 },
      { doctor: 'د. محمد رضا', appointments: 87, completion_rate: 89, avg_rating: 4.5, revenue: 52200 },
      { doctor: 'د. هالة سامي', appointments: 112, completion_rate: 93, avg_rating: 4.6, revenue: 56000 },
    ]
  })
})

app.get('/api/medications/inventory', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, name_ar: 'باراسيتامول 500ملغ', name_en: 'Paracetamol 500mg', stock: 2500, min_stock: 500, status: 'in_stock', category: 'مسكنات', expiry_date: '2027-06-15' },
      { id: 2, name_ar: 'أموكسيسيلين 250ملغ', name_en: 'Amoxicillin 250mg', stock: 180, min_stock: 200, status: 'low_stock', category: 'مضادات حيوية', expiry_date: '2026-12-01' },
      { id: 3, name_ar: 'أسيبروفين 400ملغ', name_en: 'Ibuprofen 400mg', stock: 1200, min_stock: 300, status: 'in_stock', category: 'مضادات التهاب', expiry_date: '2027-03-20' },
      { id: 4, name_ar: 'أوميبرازول 20ملغ', name_en: 'Omeprazole 20mg', stock: 50, min_stock: 100, status: 'critical', category: 'معدة', expiry_date: '2026-09-30' },
      { id: 5, name_ar: 'ميتفورمين 500ملغ', name_en: 'Metformin 500mg', stock: 800, min_stock: 200, status: 'in_stock', category: 'سكر', expiry_date: '2027-08-10' },
    ]
  })
})

app.get('/api/notifications', async (c) => {
  return c.json({
    success: true,
    data: [
      { id: 1, title_ar: 'موعد جديد محجوز', title_en: 'New Appointment Booked', body_ar: 'المريض أحمد محمد حجز موعد مع د. سارة علي', time: '5 دقائق', type: 'appointment', is_read: false },
      { id: 2, title_ar: 'تنبيه مخزون منخفض', title_en: 'Low Stock Alert', body_ar: 'أموكسيسيلين 250ملغ وصل للحد الأدنى', time: '15 دقيقة', type: 'inventory', is_read: false },
      { id: 3, title_ar: 'وصفة جاهزة للصرف', title_en: 'Prescription Ready', body_ar: 'وصفة #1234 جاهزة للصرف للصيدلية', time: '30 دقيقة', type: 'prescription', is_read: true },
      { id: 4, title_ar: 'تقرير يومي جاهز', title_en: 'Daily Report Ready', body_ar: 'تقرير أداء المستشفى ليوم 2026-08-14', time: 'ساعة', type: 'report', is_read: true },
    ]
  })
})

// ═══════════════════════════════════════════════════════════
// SERVE THE ADMIN DASHBOARD HTML
// ═══════════════════════════════════════════════════════════

const adminHTML = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>لوحة الإدارة - MediCare Pro</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { font-family: 'Cairo', sans-serif; }
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; }
::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #64748b; }
.stat-card { transition: all 0.3s ease; }
.stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(0,0,0,0.12); }
.sidebar-item { transition: all 0.2s ease; }
.sidebar-item:hover, .sidebar-item.active { background: rgba(255,255,255,0.15); border-radius: 8px; }
.table-row:hover { background: #f8fafc; }
.badge { padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.queue-card { transition: all 0.3s ease; }
.queue-card:hover { transform: scale(1.02); }
.pulse-dot { animation: pulse 2s infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
.counter { font-variant-numeric: tabular-nums; }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<div class="flex h-screen overflow-hidden">
  <!-- Sidebar -->
  <aside class="w-64 bg-gradient-to-b from-teal-700 to-teal-900 text-white flex flex-col shrink-0 shadow-xl">
    <div class="p-5 border-b border-teal-600">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-xl">🏥</div>
        <div>
          <h1 class="font-bold text-sm">MediCare Pro</h1>
          <p class="text-[10px] text-teal-200">لوحة إدارة المستشفى</p>
        </div>
      </div>
    </div>
    
    <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
      <div class="sidebar-item active flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="dashboard">
        <span>📊</span><span class="text-sm">لوحة التحكم</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="appointments">
        <span>📅</span><span class="text-sm">المواعيد</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="queue">
        <span>🎫</span><span class="text-sm">إدارة الطوابير</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="doctors">
        <span>👨‍⚕️</span><span class="text-sm">الأطباء</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="patients">
        <span>🤒</span><span class="text-sm">المرضى</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="departments">
        <span>🏢</span><span class="text-sm">الأقسام</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="pharmacy">
        <span>💊</span><span class="text-sm">الصيدلية</span>
      </div>
      <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="reports">
        <span>📈</span><span class="text-sm">التقارير</span>
      </div>
      
      <div class="mt-4 pt-4 border-t border-teal-600">
        <div class="sidebar-item flex items-center gap-3 px-3 py-2.5 cursor-pointer" data-page="settings">
          <span>⚙️</span><span class="text-sm">الإعدادات</span>
        </div>
      </div>
    </nav>
    
    <div class="p-4 border-t border-teal-600">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-teal-600 rounded-full flex items-center justify-center text-sm font-bold">م</div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold truncate">مدير المستشفى</p>
          <p class="text-[10px] text-teal-200 truncate">admin@hospital.com</p>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <!-- Top Bar -->
    <header class="h-14 bg-white border-b flex items-center justify-between px-6 shrink-0">
      <div class="flex items-center gap-4">
        <h2 class="text-lg font-bold text-gray-800" id="page-title">لوحة التحكم</h2>
        <span class="text-xs text-gray-400" id="current-date"></span>
      </div>
      <div class="flex items-center gap-4">
        <div class="relative">
          <button class="p-2 hover:bg-gray-100 rounded-lg relative" onclick="toggleNotifications()">
            🔔
            <span class="absolute -top-0.5 -left-0.5 w-4 h-4 bg-red-500 rounded-full text-[9px] text-white flex items-center justify-center pulse-dot">3</span>
          </button>
          <div id="notif-panel" class="hidden absolute left-0 top-10 w-80 bg-white rounded-xl shadow-2xl border z-50">
            <div class="p-3 border-b font-semibold text-sm">الإشعارات</div>
            <div id="notif-list" class="max-h-64 overflow-y-auto"></div>
          </div>
        </div>
        <a href="/" class="text-xs bg-teal-50 text-teal-700 px-3 py-1.5 rounded-lg hover:bg-teal-100 transition">
          ← الموقع العام
        </a>
      </div>
    </header>

    <!-- Content Area -->
    <main class="flex-1 overflow-y-auto p-6" id="content-area">
      <!-- Dashboard Page -->
      <div id="page-dashboard" class="page-content">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
          <div class="stat-card bg-white rounded-xl p-4 border shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">👥</span>
              <span class="badge bg-blue-50 text-blue-600">+12.5%</span>
            </div>
            <p class="text-2xl font-bold counter" data-target="1247">0</p>
            <p class="text-xs text-gray-500 mt-1">إجمالي المرضى</p>
          </div>
          <div class="stat-card bg-white rounded-xl p-4 border shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">👨‍⚕️</span>
              <span class="badge bg-green-50 text-green-600">+3</span>
            </div>
            <p class="text-2xl font-bold counter" data-target="42">0</p>
            <p class="text-xs text-gray-500 mt-1">الأطباء النشطين</p>
          </div>
          <div class="stat-card bg-white rounded-xl p-4 border shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">📅</span>
              <span class="badge bg-amber-50 text-amber-600">اليوم</span>
            </div>
            <p class="text-2xl font-bold counter" data-target="89">0</p>
            <p class="text-xs text-gray-500 mt-1">مواعيد اليوم</p>
          </div>
          <div class="stat-card bg-white rounded-xl p-4 border shadow-sm">
            <div class="flex items-center justify-between mb-3">
              <span class="text-2xl">💰</span>
              <span class="badge bg-emerald-50 text-emerald-600">+8.2%</span>
            </div>
            <p class="text-2xl font-bold counter" data-target="284500">0</p>
            <p class="text-xs text-gray-500 mt-1">إيرادات الشهر (ر.س)</p>
          </div>
        </div>

        <!-- Revenue Chart Area -->
        <div class="grid lg:grid-cols-3 gap-4 mb-6">
          <div class="lg:col-span-2 bg-white rounded-xl p-5 border shadow-sm">
            <h3 class="font-semibold text-sm mb-4">📈 الإيرادات والمصروفات (8 أشهر)</h3>
            <div class="flex items-end gap-2 h-48" id="revenue-chart"></div>
            <div class="flex justify-center gap-4 mt-3 text-xs">
              <span class="flex items-center gap-1"><span class="w-3 h-3 bg-teal-500 rounded-full"></span>الإيرادات</span>
              <span class="flex items-center gap-1"><span class="w-3 h-3 bg-gray-300 rounded-full"></span>المصروفات</span>
            </div>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm">
            <h3 class="font-semibold text-sm mb-4">📊 حالة اليوم</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">مكتملة</span>
                <span class="font-bold text-green-600">56</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-green-500 rounded-full h-2" style="width:63%"></div></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">قيد التنفيذ</span>
                <span class="font-bold text-blue-600">18</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-blue-500 rounded-full h-2" style="width:20%"></div></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">في الانتظار</span>
                <span class="font-bold text-amber-600">9</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-amber-500 rounded-full h-2" style="width:10%"></div></div>
              <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500">ملغاة</span>
                <span class="font-bold text-red-600">6</span>
              </div>
              <div class="w-full bg-gray-100 rounded-full h-2"><div class="bg-red-500 rounded-full h-2" style="width:7%"></div></div>
            </div>
            <div class="mt-4 pt-4 border-t">
              <div class="flex justify-between text-xs">
                <span class="text-gray-500">متوسط وقت الانتظار</span>
                <span class="font-bold">18 دقيقة</span>
              </div>
              <div class="flex justify-between text-xs mt-1">
                <span class="text-gray-500">رضا المرضى</span>
                <span class="font-bold text-amber-500">⭐ 4.6/5</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Today's Appointments Table -->
        <div class="bg-white rounded-xl border shadow-sm">
          <div class="p-5 border-b flex justify-between items-center">
            <h3 class="font-semibold text-sm">📅 مواعيد اليوم</h3>
            <span class="badge bg-teal-50 text-teal-600">89 موعد</span>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead class="bg-gray-50 text-gray-500 text-xs">
                <tr>
                  <th class="text-right p-3">#</th>
                  <th class="text-right p-3">المريض</th>
                  <th class="text-right p-3">الطبيب</th>
                  <th class="text-right p-3">القسم</th>
                  <th class="text-right p-3">الوقت</th>
                  <th class="text-right p-3">النوع</th>
                  <th class="text-right p-3">الحالة</th>
                </tr>
              </thead>
              <tbody id="appointments-table"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Queue Page -->
      <div id="page-queue" class="page-content hidden">
        <div class="grid lg:grid-cols-3 gap-4 mb-6">
          <div class="bg-white rounded-xl p-5 border shadow-sm text-center">
            <p class="text-4xl font-bold text-teal-600">D1-012</p>
            <p class="text-xs text-gray-500 mt-1">يُخدم الآن</p>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm text-center">
            <p class="text-4xl font-bold text-blue-600">D1-013</p>
            <p class="text-xs text-gray-500 mt-1">التالي</p>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm text-center">
            <p class="text-4xl font-bold text-amber-600">8</p>
            <p class="text-xs text-gray-500 mt-1">في الانتظار</p>
          </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-4 mb-6">
          <div class="bg-white rounded-xl p-5 border shadow-sm">
            <h3 class="font-semibold text-sm mb-4">🎫 قائمة الانتظار - القلب والأوعية</h3>
            <div id="queue-list" class="space-y-2"></div>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm">
            <h3 class="font-semibold text-sm mb-4">🎬 إجراءات سريعة</h3>
            <div class="grid grid-cols-2 gap-3">
              <button class="bg-teal-500 hover:bg-teal-600 text-white rounded-xl p-4 text-center transition">
                <p class="text-2xl mb-1">📞</p>
                <p class="text-xs font-semibold">استدعاء التالي</p>
              </button>
              <button class="bg-amber-500 hover:bg-amber-600 text-white rounded-xl p-4 text-center transition">
                <p class="text-2xl mb-1">⏭️</p>
                <p class="text-xs font-semibold">تخطي المريض</p>
              </button>
              <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-xl p-4 text-center transition">
                <p class="text-2xl mb-1">🔄</p>
                <p class="text-xs font-semibold">تحويل لقسم</p>
              </button>
              <button class="bg-purple-500 hover:bg-purple-600 text-white rounded-xl p-4 text-center transition">
                <p class="text-2xl mb-1">✅</p>
                <p class="text-xs font-semibold">إنهاء الخدمة</p>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Doctors Page -->
      <div id="page-doctors" class="page-content hidden">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold">إدارة الأطباء</h3>
          <button class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm transition">+ إضافة طبيب</button>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4" id="doctors-grid"></div>
      </div>

      <!-- Patients Page -->
      <div id="page-patients" class="page-content hidden">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold">إدارة المرضى</h3>
          <button class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm transition">+ تسجيل مريض</button>
        </div>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs">
              <tr>
                <th class="text-right p-3">#</th>
                <th class="text-right p-3">الاسم</th>
                <th class="text-right p-3">الهاتف</th>
                <th class="text-right p-3">فصيلة الدم</th>
                <th class="text-right p-3">آخر زيارة</th>
                <th class="text-right p-3">الزيارات</th>
                <th class="text-right p-3">الحالة</th>
                <th class="text-right p-3">إجراءات</th>
              </tr>
            </thead>
            <tbody id="patients-table"></tbody>
          </table>
        </div>
      </div>

      <!-- Departments Page -->
      <div id="page-departments" class="page-content hidden">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold">إدارة الأقسام</h3>
          <button class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm transition">+ إضافة قسم</button>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4" id="departments-grid"></div>
      </div>

      <!-- Pharmacy Page -->
      <div id="page-pharmacy" class="page-content hidden">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold">إدارة الصيدلية والمخزون</h3>
          <button class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm transition">+ إضافة دواء</button>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4" id="medications-grid"></div>
      </div>

      <!-- Reports Page -->
      <div id="page-reports" class="page-content hidden">
        <h3 class="font-semibold mb-4">التقارير والتحليلات</h3>
        <div class="grid md:grid-cols-2 gap-4 mb-6">
          <div class="bg-white rounded-xl p-5 border shadow-sm">
            <h4 class="text-sm font-semibold mb-3">🏆 أداء الأطباء</h4>
            <div class="space-y-2" id="doctor-performance"></div>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm">
            <h4 class="text-sm font-semibold mb-3">📊 تصدير التقارير</h4>
            <div class="space-y-2">
              <button class="w-full flex items-center gap-3 p-3 rounded-lg border hover:bg-gray-50 transition text-sm">
                <span class="text-xl">📄</span>
                <div class="flex-1 text-right">
                  <p class="font-semibold">تقرير المواعيد اليومي</p>
                  <p class="text-[10px] text-gray-400">PDF - Excel</p>
                </div>
                <span>⬇️</span>
              </button>
              <button class="w-full flex items-center gap-3 p-3 rounded-lg border hover:bg-gray-50 transition text-sm">
                <span class="text-xl">💰</span>
                <div class="flex-1 text-right">
                  <p class="font-semibold">تقرير الإيرادات الشهري</p>
                  <p class="text-[10px] text-gray-400">PDF - Excel</p>
                </div>
                <span>⬇️</span>
              </button>
              <button class="w-full flex items-center gap-3 p-3 rounded-lg border hover:bg-gray-50 transition text-sm">
                <span class="text-xl">👨‍⚕️</span>
                <div class="flex-1 text-right">
                  <p class="font-semibold">تقرير أداء الأطباء</p>
                  <p class="text-[10px] text-gray-400">PDF - Excel</p>
                </div>
                <span>⬇️</span>
              </button>
              <button class="w-full flex items-center gap-3 p-3 rounded-lg border hover:bg-gray-50 transition text-sm">
                <span class="text-xl">💊</span>
                <div class="flex-1 text-right">
                  <p class="font-semibold">تقرير المخزون والأدوية</p>
                  <p class="text-[10px] text-gray-400">PDF - Excel</p>
                </div>
                <span>⬇️</span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Settings Page -->
      <div id="page-settings" class="page-content hidden">
        <h3 class="font-semibold mb-4">إعدادات المستشفى</h3>
        <div class="grid md:grid-cols-2 gap-4">
          <div class="bg-white rounded-xl p-5 border shadow-sm space-y-4">
            <h4 class="text-sm font-semibold">معلومات المستشفى</h4>
            <div><label class="text-xs text-gray-500">اسم المستشفى (عربي)</label><input class="w-full border rounded-lg p-2 text-sm mt-1" value="مستشفى ميدي كير المركزي"></div>
            <div><label class="text-xs text-gray-500">اسم المستشفى (إنجليزي)</label><input class="w-full border rounded-lg p-2 text-sm mt-1" value="MediCare Central Hospital"></div>
            <div><label class="text-xs text-gray-500">البريد الإلكتروني</label><input class="w-full border rounded-lg p-2 text-sm mt-1" value="info@medicare-hospital.com"></div>
            <div><label class="text-xs text-gray-500">رقم الهاتف</label><input class="w-full border rounded-lg p-2 text-sm mt-1" value="+966123456789"></div>
            <button class="w-full bg-teal-500 hover:bg-teal-600 text-white rounded-lg py-2 text-sm transition">حفظ التغييرات</button>
          </div>
          <div class="bg-white rounded-xl p-5 border shadow-sm space-y-4">
            <h4 class="text-sm font-semibold">إعدادات النظام</h4>
            <div class="flex justify-between items-center p-3 rounded-lg border">
              <div><p class="text-sm font-semibold">اللغة الافتراضية</p><p class="text-[10px] text-gray-400">لغة واجهة المستخدم</p></div>
              <select class="border rounded-lg p-1.5 text-sm"><option>العربية</option><option>English</option></select>
            </div>
            <div class="flex justify-between items-center p-3 rounded-lg border">
              <div><p class="text-sm font-semibold">الإشعارات</p><p class="text-[10px] text-gray-400">إشعارات FCM و SMS</p></div>
              <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" checked class="sr-only peer"><div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-teal-300 rounded-full peer peer-checked:bg-teal-500"></div></label>
            </div>
            <div class="flex justify-between items-center p-3 rounded-lg border">
              <div><p class="text-sm font-semibold">إعادة تعيين الطوابير</p><p class="text-[10px] text-gray-400">يومياً عند منتصف الليل</p></div>
              <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" checked class="sr-only peer"><div class="w-9 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-teal-300 rounded-full peer peer-checked:bg-teal-500"></div></label>
            </div>
          </div>
        </div>
      </div>

      <!-- Appointments Page -->
      <div id="page-appointments" class="page-content hidden">
        <div class="flex justify-between items-center mb-4">
          <h3 class="font-semibold">إدارة المواعيد</h3>
          <div class="flex gap-2">
            <button class="bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-xs transition">اليوم</button>
            <button class="bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-xs transition">هذا الأسبوع</button>
            <button class="bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg text-xs transition">هذا الشهر</button>
          </div>
        </div>
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
          <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs">
              <tr>
                <th class="text-right p-3">#</th>
                <th class="text-right p-3">المريض</th>
                <th class="text-right p-3">الطبيب</th>
                <th class="text-right p-3">القسم</th>
                <th class="text-right p-3">الوقت</th>
                <th class="text-right p-3">النوع</th>
                <th class="text-right p-3">الحالة</th>
                <th class="text-right p-3">إجراءات</th>
              </tr>
            </thead>
            <tbody id="all-appointments-table"></tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>

<script>
// Current date
document.getElementById('current-date').textContent = new Date().toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

// Navigation
const sidebarItems = document.querySelectorAll('.sidebar-item[data-page]');
const pages = document.querySelectorAll('.page-content');
const pageTitle = document.getElementById('page-title');

const titles = {
  dashboard: 'لوحة التحكم',
  appointments: 'إدارة المواعيد',
  queue: 'إدارة الطوابير',
  doctors: 'إدارة الأطباء',
  patients: 'إدارة المرضى',
  departments: 'إدارة الأقسام',
  pharmacy: 'الصيدلية والمخزون',
  reports: 'التقارير والتحليلات',
  settings: 'الإعدادات'
};

sidebarItems.forEach(item => {
  item.addEventListener('click', () => {
    sidebarItems.forEach(i => i.classList.remove('active'));
    item.classList.add('active');
    pages.forEach(p => p.classList.add('hidden'));
    const page = item.dataset.page;
    document.getElementById('page-' + page).classList.remove('hidden');
    pageTitle.textContent = titles[page] || '';
  });
});

// Counter animation
function animateCounters() {
  document.querySelectorAll('.counter').forEach(el => {
    const target = parseInt(el.dataset.target);
    const duration = 1500;
    const start = performance.now();
    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(target * eased).toLocaleString('ar-SA');
      if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
  });
}

// Revenue Chart
function renderRevenueChart() {
  const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس'];
  const revenue = [185, 210, 195, 245, 260, 278, 290, 284];
  const expenses = [120, 135, 128, 145, 155, 162, 170, 168];
  const maxVal = Math.max(...revenue) * 1.1;
  const chart = document.getElementById('revenue-chart');
  
  months.forEach((month, i) => {
    const col = document.createElement('div');
    col.className = 'flex-1 flex flex-col items-center gap-1';
    const rH = (revenue[i] / maxVal * 100);
    const eH = (expenses[i] / maxVal * 100);
    col.innerHTML = \`
      <div class="w-full flex items-end gap-0.5 justify-center" style="height:160px">
        <div class="w-3 bg-teal-500 rounded-t transition-all duration-700" style="height:\${rH}%" title="إيرادات: \${revenue[i]}K"></div>
        <div class="w-3 bg-gray-300 rounded-t transition-all duration-700" style="height:\${eH}%" title="مصروفات: \${expenses[i]}K"></div>
      </div>
      <span class="text-[9px] text-gray-400">\${month}</span>
    \`;
    chart.appendChild(col);
  });
}

// Appointments table
const statusColors = {
  completed: 'bg-green-50 text-green-600',
  in_progress: 'bg-blue-50 text-blue-600',
  waiting: 'bg-amber-50 text-amber-600',
  confirmed: 'bg-teal-50 text-teal-600',
  pending: 'bg-gray-50 text-gray-600',
  cancelled: 'bg-red-50 text-red-600',
  no_show: 'bg-orange-50 text-orange-600'
};
const statusLabels = {
  completed: 'مكتمل', in_progress: 'قيد التنفيذ', waiting: 'في الانتظار',
  confirmed: 'مؤكد', pending: 'قيد الانتظار', cancelled: 'ملغى', no_show: 'لم يحضر'
};
const typeLabels = { consultation: 'استشارة', follow_up: 'متابعة' };

function renderAppointments() {
  const appointments = [
    { id: 1, patient: 'أحمد محمد', doctor: 'د. سارة علي', department: 'بطانة', time: '08:00', status: 'completed', type: 'consultation' },
    { id: 2, patient: 'فاطمة حسن', doctor: 'د. خالد يوسف', department: 'قلب', time: '08:30', status: 'in_progress', type: 'follow_up' },
    { id: 3, patient: 'عمر أحمد', doctor: 'د. سارة علي', department: 'بطانة', time: '09:00', status: 'waiting', type: 'consultation' },
    { id: 4, patient: 'نور الدين', doctor: 'د. منى عبدالله', department: 'أطفال', time: '09:30', status: 'waiting', type: 'consultation' },
    { id: 5, patient: 'سلمى خالد', doctor: 'د. خالد يوسف', department: 'قلب', time: '10:00', status: 'confirmed', type: 'follow_up' },
    { id: 6, patient: 'يوسف إبراهيم', doctor: 'د. محمد رضا', department: 'عظام', time: '10:30', status: 'confirmed', type: 'consultation' },
    { id: 7, patient: 'هدى محمد', doctor: 'د. منى عبدالله', department: 'أطفال', time: '11:00', status: 'pending', type: 'consultation' },
    { id: 8, patient: 'محمود علي', doctor: 'د. سارة علي', department: 'بطانة', time: '11:30', status: 'pending', type: 'follow_up' },
  ];

  ['appointments-table', 'all-appointments-table'].forEach(tableId => {
    const tbody = document.getElementById(tableId);
    if (!tbody) return;
    tbody.innerHTML = appointments.map(a => \`
      <tr class="table-row border-b">
        <td class="p-3 text-gray-400">\${a.id}</td>
        <td class="p-3 font-medium">\${a.patient}</td>
        <td class="p-3 text-gray-600">\${a.doctor}</td>
        <td class="p-3 text-gray-500">\${a.department}</td>
        <td class="p-3 text-gray-500">\${a.time}</td>
        <td class="p-3"><span class="badge bg-gray-100">\${typeLabels[a.type]}</span></td>
        <td class="p-3"><span class="badge \${statusColors[a.status]}">\${statusLabels[a.status]}</span></td>
        <td class="p-3">
          \${tableId === 'all-appointments-table' ? '<button class="text-teal-600 hover:text-teal-700 text-xs">تفاصيل</button>' : ''}
        </td>
      </tr>
    \`).join('');
  });
}

// Queue list
function renderQueue() {
  const queueList = document.getElementById('queue-list');
  if (!queueList) return;
  const patients = [
    { number: 'D1-009', name: 'أحمد محمد', status: 'in_progress', wait: 0, priority: false },
    { number: 'D1-010', name: 'فاطمة حسن', status: 'waiting', wait: 15, priority: false },
    { number: 'D1-011', name: 'عمر أحمد', status: 'waiting', wait: 30, priority: true },
    { number: 'D1-012', name: 'نور الدين', status: 'waiting', wait: 45, priority: false },
    { number: 'D1-013', name: 'سلمى خالد', status: 'waiting', wait: 52, priority: false },
  ];
  queueList.innerHTML = patients.map(p => \`
    <div class="queue-card flex items-center gap-3 p-3 rounded-xl border \${p.status === 'in_progress' ? 'bg-teal-50 border-teal-200' : 'bg-white'}">
      <div class="w-10 h-10 rounded-lg \${p.status === 'in_progress' ? 'bg-teal-500 text-white' : 'bg-gray-100 text-gray-600'} flex items-center justify-center text-sm font-bold">
        \${p.number}
      </div>
      <div class="flex-1">
        <p class="text-sm font-semibold">\${p.name} \${p.priority ? '<span class="badge bg-red-100 text-red-600 text-[9px]">أولوية</span>' : ''}</p>
        <p class="text-[10px] text-gray-400">\${p.wait > 0 ? 'انتظار: ' + p.wait + ' دقيقة' : 'يُخدم الآن'}</p>
      </div>
      <span class="badge \${statusColors[p.status]}">\${statusLabels[p.status]}</span>
    </div>
  \`).join('');
}

// Doctors grid
function renderDoctors() {
  const grid = document.getElementById('doctors-grid');
  if (!grid) return;
  const doctors = [
    { id: 1, name: 'د. سارة علي', specialty: 'أمراض البطانة', dept: 'الباطنة', patients: 342, rating: 4.8, status: 'active' },
    { id: 2, name: 'د. خالد يوسف', specialty: 'جراحة القلب', dept: 'القلب', patients: 289, rating: 4.9, status: 'active' },
    { id: 3, name: 'د. منى عبدالله', specialty: 'طب الأطفال', dept: 'الأطفال', patients: 198, rating: 4.7, status: 'active' },
    { id: 4, name: 'د. محمد رضا', specialty: 'جراحة العظام', dept: 'العظام', patients: 156, rating: 4.5, status: 'active' },
    { id: 5, name: 'د. هالة سامي', specialty: 'أمراض جلدية', dept: 'الجلدية', patients: 267, rating: 4.6, status: 'on_leave' },
    { id: 6, name: 'د. عمرو حسين', specialty: 'طب العيون', dept: 'العيون', patients: 312, rating: 4.4, status: 'active' },
  ];
  grid.innerHTML = doctors.map(d => \`
    <div class="bg-white rounded-xl border shadow-sm p-5">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-12 h-12 rounded-full \${d.status === 'active' ? 'bg-teal-100' : 'bg-gray-100'} flex items-center justify-center text-xl">👨‍⚕️</div>
        <div class="flex-1">
          <p class="font-semibold text-sm">\${d.name}</p>
          <p class="text-[10px] text-gray-500">\${d.specialty} - \${d.dept}</p>
        </div>
        <span class="badge \${d.status === 'active' ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500'}">\${d.status === 'active' ? 'نشط' : 'إجازة'}</span>
      </div>
      <div class="flex justify-between text-xs text-gray-500 border-t pt-3">
        <span>\${d.patients} مريض</span>
        <span>⭐ \${d.rating}</span>
      </div>
    </div>
  \`).join('');
}

// Patients table
function renderPatients() {
  const tbody = document.getElementById('patients-table');
  if (!tbody) return;
  const patients = [
    { id: 1, name: 'أحمد محمد علي', phone: '+966501234567', blood: 'A+', last: '2026-08-14', visits: 12, status: 'active' },
    { id: 2, name: 'فاطمة حسن محمود', phone: '+966509876543', blood: 'O+', last: '2026-08-14', visits: 8, status: 'active' },
    { id: 3, name: 'عمر أحمد سعيد', phone: '+966503456789', blood: 'B+', last: '2026-08-13', visits: 5, status: 'active' },
    { id: 4, name: 'نور الدين محمد', phone: '+966507654321', blood: 'AB+', last: '2026-08-12', visits: 15, status: 'active' },
    { id: 5, name: 'سلمى خالد عبدالله', phone: '+966502345678', blood: 'O-', last: '2026-08-10', visits: 3, status: 'inactive' },
  ];
  tbody.innerHTML = patients.map(p => \`
    <tr class="table-row border-b">
      <td class="p-3 text-gray-400">\${p.id}</td>
      <td class="p-3 font-medium">\${p.name}</td>
      <td class="p-3 text-gray-500 text-xs" dir="ltr">\${p.phone}</td>
      <td class="p-3"><span class="badge bg-red-50 text-red-600">\${p.blood}</span></td>
      <td class="p-3 text-gray-500 text-xs">\${p.last}</td>
      <td class="p-3 text-gray-500">\${p.visits}</td>
      <td class="p-3"><span class="badge \${p.status === 'active' ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500'}">\${p.status === 'active' ? 'نشط' : 'غير نشط'}</span></td>
      <td class="p-3"><button class="text-teal-600 text-xs hover:text-teal-700">عرض</button></td>
    </tr>
  \`).join('');
}

// Departments
function renderDepartments() {
  const grid = document.getElementById('departments-grid');
  if (!grid) return;
  const depts = [
    { name: 'القلب والأوعية الدموية', doctors: 6, today: 18, icon: '❤️' },
    { name: 'الباطنة', doctors: 5, today: 24, icon: '🫁' },
    { name: 'طب الأطفال', doctors: 4, today: 15, icon: '👶' },
    { name: 'جراحة العظام', doctors: 3, today: 12, icon: '🦴' },
    { name: 'الأمراض الجلدية', doctors: 3, today: 9, icon: '🧴' },
    { name: 'طب العيون', doctors: 4, today: 11, icon: '👁️' },
    { name: 'الأنف والأذن والحنجرة', doctors: 3, today: 7, icon: '👂' },
    { name: 'الأشعة والتشخيص', doctors: 2, today: 20, icon: '📡' },
  ];
  grid.innerHTML = depts.map(d => \`
    <div class="bg-white rounded-xl border shadow-sm p-5 text-center">
      <span class="text-3xl">\${d.icon}</span>
      <p class="font-semibold text-sm mt-2">\${d.name}</p>
      <div class="flex justify-center gap-4 mt-3 text-xs text-gray-500">
        <span>\${d.doctors} أطباء</span>
        <span>\${d.today} مريض اليوم</span>
      </div>
    </div>
  \`).join('');
}

// Medications
function renderMedications() {
  const grid = document.getElementById('medications-grid');
  if (!grid) return;
  const meds = [
    { name: 'باراسيتامول 500ملغ', stock: 2500, min: 500, status: 'in_stock', cat: 'مسكنات', expiry: '2027-06-15' },
    { name: 'أموكسيسيلين 250ملغ', stock: 180, min: 200, status: 'low_stock', cat: 'مضادات حيوية', expiry: '2026-12-01' },
    { name: 'أسيبروفين 400ملغ', stock: 1200, min: 300, status: 'in_stock', cat: 'مضادات التهاب', expiry: '2027-03-20' },
    { name: 'أوميبرازول 20ملغ', stock: 50, min: 100, status: 'critical', cat: 'معدة', expiry: '2026-09-30' },
    { name: 'ميتفورمين 500ملغ', stock: 800, min: 200, status: 'in_stock', cat: 'سكر', expiry: '2027-08-10' },
  ];
  const stockColors = { in_stock: 'bg-green-50 text-green-600 border-green-200', low_stock: 'bg-amber-50 text-amber-600 border-amber-200', critical: 'bg-red-50 text-red-600 border-red-200' };
  const stockLabels = { in_stock: 'متوفر', low_stock: 'منخفض', critical: 'حرج' };
  grid.innerHTML = meds.map(m => \`
    <div class="rounded-xl border p-5 \${stockColors[m.status]}">
      <div class="flex justify-between items-start mb-2">
        <div>
          <p class="font-semibold text-sm">\${m.name}</p>
          <p class="text-[10px] opacity-75">\${m.cat} | ينتهي: \${m.expiry}</p>
        </div>
        <span class="badge \${stockColors[m.status]}">\${stockLabels[m.status]}</span>
      </div>
      <div class="mt-3">
        <div class="flex justify-between text-xs mb-1">
          <span>المخزون</span>
          <span>\${m.stock} / \${m.min} (حد أدنى)</span>
        </div>
        <div class="w-full bg-white/50 rounded-full h-2">
          <div class="rounded-full h-2 \${m.status === 'critical' ? 'bg-red-500' : m.status === 'low_stock' ? 'bg-amber-500' : 'bg-green-500'}" style="width: \${Math.min((m.stock / (m.min * 5)) * 100, 100)}%"></div>
        </div>
      </div>
    </div>
  \`).join('');
}

// Doctor Performance
function renderDoctorPerformance() {
  const el = document.getElementById('doctor-performance');
  if (!el) return;
  const data = [
    { name: 'د. خالد يوسف', rate: 97, rating: 4.9, revenue: 92400 },
    { name: 'د. سارة علي', rate: 94, rating: 4.8, revenue: 78000 },
    { name: 'د. هالة سامي', rate: 93, rating: 4.6, revenue: 56000 },
    { name: 'د. منى عبدالله', rate: 91, rating: 4.7, revenue: 49000 },
    { name: 'د. محمد رضا', rate: 89, rating: 4.5, revenue: 52200 },
  ];
  el.innerHTML = data.map(d => \`
    <div class="flex items-center gap-3 p-2 rounded-lg">
      <span class="text-sm font-medium w-28 truncate">\${d.name}</span>
      <div class="flex-1">
        <div class="w-full bg-gray-100 rounded-full h-2">
          <div class="bg-teal-500 rounded-full h-2" style="width: \${d.rate}%"></div>
        </div>
      </div>
      <span class="text-xs font-bold text-teal-600 w-10">\${d.rate}%</span>
      <span class="text-[10px] text-gray-400">⭐ \${d.rating}</span>
    </div>
  \`).join('');
}

// Notifications
function toggleNotifications() {
  const panel = document.getElementById('notif-panel');
  panel.classList.toggle('hidden');
}
document.addEventListener('click', (e) => {
  const panel = document.getElementById('notif-panel');
  if (!e.target.closest('[onclick]') && !e.target.closest('#notif-panel')) {
    panel.classList.add('hidden');
  }
});
const notifs = [
  { title: 'موعد جديد محجوز', body: 'المريض أحمد محمد حجز موعد مع د. سارة علي', time: '5 دقائق', read: false },
  { title: 'تنبيه مخزون منخفض', body: 'أموكسيسيلين 250ملغ وصل للحد الأدنى', time: '15 دقيقة', read: false },
  { title: 'وصفة جاهزة للصرف', body: 'وصفة #1234 جاهزة للصرف للصيدلية', time: '30 دقيقة', read: false },
];
document.getElementById('notif-list').innerHTML = notifs.map(n => \`
  <div class="p-3 border-b hover:bg-gray-50 cursor-pointer \${n.read ? 'opacity-60' : ''}">
    <div class="flex justify-between">
      <p class="text-xs font-semibold">\${n.title}</p>
      <span class="text-[10px] text-gray-400">\${n.time}</span>
    </div>
    <p class="text-[11px] text-gray-500 mt-0.5">\${n.body}</p>
  </div>
\`).join('');

// Init
animateCounters();
renderRevenueChart();
renderAppointments();
renderQueue();
renderDoctors();
renderPatients();
renderDepartments();
renderMedications();
renderDoctorPerformance();
</script>
</body>
</html>`

app.get('/', (c) => c.html(adminHTML))

console.log('🏥 MediCare Pro Admin Dashboard running on http://localhost:3001')

export default {
  port: 3001,
  fetch: app.fetch,
}
