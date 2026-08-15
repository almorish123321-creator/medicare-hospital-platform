import { Hono } from 'hono'
import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient({
  datasourceUrl: 'file:/home/z/my-project/db/custom.db',
})

const app = new Hono()

// ============================================================
// API ENDPOINTS
// ============================================================

// Dashboard Stats
app.get('/api/dashboard/stats', async (c) => {
  try {
    const [hospitals, doctors, appointments, completedAppointments] = await Promise.all([
      prisma.hospital.count({ where: { active: true } }),
      prisma.doctor.count({ where: { available: true } }),
      prisma.appointment.count({ where: { date: new Date().toISOString().split('T')[0] } }),
      prisma.appointment.findMany({ where: { status: 'completed' } }),
    ])
    const revenue = completedAppointments.reduce((sum, a) => {
      return sum + (a.doctorId ? 0 : 0)
    }, 0)
    const totalRevenue = await prisma.doctor.aggregate({
      _sum: { price: true },
    })
    const completedCount = await prisma.appointment.count({ where: { status: 'completed' } })
    return c.json({
      totalHospitals: hospitals,
      totalDoctors: doctors,
      todayAppointments: appointments,
      totalRevenue: (totalRevenue._sum.price || 0) * completedCount || 0,
      totalDepartments: await prisma.department.count({ where: { active: true } }),
      totalAppointments: await prisma.appointment.count(),
    })
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

// Hospitals CRUD
app.get('/api/hospitals', async (c) => {
  try {
    const hospitals = await prisma.hospital.findMany({
      include: {
        _count: { select: { departmentsList: true, doctorsList: true } },
      },
      orderBy: { createdAt: 'desc' },
    })
    return c.json(hospitals)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.post('/api/hospitals', async (c) => {
  try {
    const body = await c.req.json()
    const hospital = await prisma.hospital.create({ data: body })
    return c.json(hospital, 201)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.put('/api/hospitals/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const hospital = await prisma.hospital.update({ where: { id }, data: body })
    return c.json(hospital)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.delete('/api/hospitals/:id', async (c) => {
  try {
    const id = c.req.param('id')
    await prisma.appointment.deleteMany({ where: { hospitalId: id } })
    const doctors = await prisma.doctor.findMany({ where: { hospitalId: id }, select: { id: true } })
    for (const doc of doctors) {
      await prisma.schedule.deleteMany({ where: { doctorId: doc.id } })
      await prisma.appointment.deleteMany({ where: { doctorId: doc.id } })
    }
    await prisma.doctor.deleteMany({ where: { hospitalId: id } })
    await prisma.department.deleteMany({ where: { hospitalId: id } })
    await prisma.hospital.delete({ where: { id } })
    return c.json({ success: true })
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

// Doctors CRUD
app.get('/api/doctors', async (c) => {
  try {
    const hospitalId = c.req.query('hospitalId')
    const departmentId = c.req.query('departmentId')
    const where: any = {}
    if (hospitalId) where.hospitalId = hospitalId
    if (departmentId) where.departmentId = departmentId
    const doctors = await prisma.doctor.findMany({
      where,
      include: {
        hospital: { select: { name: true, nameEn: true } },
        department: { select: { name: true, nameEn: true } },
      },
      orderBy: { createdAt: 'desc' },
    })
    return c.json(doctors)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.post('/api/doctors', async (c) => {
  try {
    const body = await c.req.json()
    const doctor = await prisma.doctor.create({ data: body })
    return c.json(doctor, 201)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.put('/api/doctors/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const doctor = await prisma.doctor.update({ where: { id }, data: body })
    return c.json(doctor)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.delete('/api/doctors/:id', async (c) => {
  try {
    const id = c.req.param('id')
    await prisma.schedule.deleteMany({ where: { doctorId: id } })
    await prisma.appointment.deleteMany({ where: { doctorId: id } })
    await prisma.doctor.delete({ where: { id } })
    return c.json({ success: true })
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

// Appointments
app.get('/api/appointments', async (c) => {
  try {
    const status = c.req.query('status')
    const where: any = {}
    if (status) where.status = status
    const appointments = await prisma.appointment.findMany({
      where,
      include: {
        doctor: {
          include: {
            hospital: { select: { name: true, nameEn: true } },
          },
        },
      },
      orderBy: { createdAt: 'desc' },
    })
    return c.json(appointments)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.put('/api/appointments/:id', async (c) => {
  try {
    const id = c.req.param('id')
    const body = await c.req.json()
    const appointment = await prisma.appointment.update({ where: { id }, data: body })
    return c.json(appointment)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

app.delete('/api/appointments/:id', async (c) => {
  try {
    const id = c.req.param('id')
    await prisma.appointment.delete({ where: { id } })
    return c.json({ success: true })
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

// Departments
app.get('/api/departments', async (c) => {
  try {
    const departments = await prisma.department.findMany({
      include: {
        hospital: { select: { name: true, nameEn: true } },
        _count: { select: { doctors: true } },
      },
      orderBy: { createdAt: 'desc' },
    })
    return c.json(departments)
  } catch (e: any) {
    return c.json({ error: e.message }, 500)
  }
})

// ============================================================
// HTML DASHBOARD
// ============================================================

const HTML = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MediCare Pro - لوحة التحكم</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --sidebar-bg:#0f172a;
  --sidebar-text:#cbd5e1;
  --sidebar-active:#10b981;
  --main-bg:#f8fafc;
  --card-bg:#ffffff;
  --primary:#10b981;
  --primary-hover:#059669;
  --accent:#14b8a6;
  --text-primary:#1e293b;
  --text-secondary:#64748b;
  --border:#e2e8f0;
  --danger:#ef4444;
  --danger-hover:#dc2626;
  --warning:#f59e0b;
  --info:#3b82f6;
  --success:#10b981;
  --radius:12px;
  --shadow:0 1px 3px rgba(0,0,0,0.08),0 1px 2px rgba(0,0,0,0.06);
  --shadow-lg:0 10px 15px -3px rgba(0,0,0,0.1),0 4px 6px -4px rgba(0,0,0,0.1);
}
html[dir="ltr"]{direction:ltr;text-align:left}
html[dir="rtl"]{direction:rtl;text-align:right}
body{font-family:system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--main-bg);color:var(--text-primary);line-height:1.6;min-height:100vh;display:flex;overflow-x:hidden}
a{text-decoration:none;color:inherit}
button{cursor:pointer;font-family:inherit}
input,select,textarea{font-family:inherit;font-size:14px}

/* Sidebar */
.sidebar{width:260px;min-height:100vh;background:var(--sidebar-bg);color:var(--sidebar-text);display:flex;flex-direction:column;position:fixed;top:0;right:0;z-index:50;transition:transform .3s ease}
html[dir="ltr"] .sidebar{right:auto;left:0}
.sidebar-header{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.08)}
.sidebar-logo{font-size:22px;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px}
.sidebar-logo svg{flex-shrink:0}
.sidebar-logo span{background:linear-gradient(135deg,var(--primary),var(--accent));-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.sidebar-subtitle{font-size:12px;color:var(--sidebar-text);opacity:.6;margin-top:4px}
.sidebar-nav{flex:1;padding:16px 12px;display:flex;flex-direction:column;gap:4px}
.nav-item{display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;color:var(--sidebar-text);cursor:pointer;transition:all .2s ease;font-size:14px;font-weight:500;border:none;background:none;width:100%;text-align:inherit}
.nav-item:hover{background:rgba(255,255,255,0.06);color:#fff}
.nav-item.active{background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;box-shadow:0 4px 12px rgba(16,185,129,0.3)}
.nav-item svg{flex-shrink:0;opacity:.7}
.nav-item.active svg,.nav-item:hover svg{opacity:1}
.sidebar-footer{padding:16px 12px;border-top:1px solid rgba(255,255,255,0.08)}
.lang-toggle{display:flex;background:rgba(255,255,255,0.06);border-radius:8px;overflow:hidden}
.lang-btn{flex:1;padding:8px;text-align:center;font-size:13px;font-weight:500;border:none;background:none;color:var(--sidebar-text);cursor:pointer;transition:all .2s}
.lang-btn.active{background:var(--primary);color:#fff}

/* Main Content */
.main{flex:1;margin-right:260px;min-height:100vh;transition:margin .3s ease}
html[dir="ltr"] .main{margin-right:0;margin-left:260px}
.topbar{background:var(--card-bg);border-bottom:1px solid var(--border);padding:16px 24px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:40}
.hamburger{display:none;background:none;border:none;font-size:24px;color:var(--text-primary);padding:4px}
.topbar-title{font-size:18px;font-weight:700;color:var(--text-primary)}
.topbar-actions{display:flex;align-items:center;gap:12px}
.content{padding:24px}

/* Cards */
.card{background:var(--card-bg);border-radius:var(--radius);border:1px solid var(--border);box-shadow:var(--shadow)}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:24px}
.stat-card{padding:20px;display:flex;align-items:center;gap:16px}
.stat-icon{width:52px;height:52px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.stat-icon.green{background:rgba(16,185,129,0.1);color:var(--primary)}
.stat-icon.teal{background:rgba(20,184,166,0.1);color:var(--accent)}
.stat-icon.blue{background:rgba(59,130,246,0.1);color:var(--info)}
.stat-icon.amber{background:rgba(245,158,11,0.1);color:var(--warning)}
.stat-value{font-size:28px;font-weight:800;color:var(--text-primary);line-height:1.2}
.stat-label{font-size:13px;color:var(--text-secondary);margin-top:2px}

/* Table */
.table-container{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead{background:#f1f5f9}
th{padding:12px 16px;text-align:right;font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
html[dir="ltr"] th{text-align:left}
td{padding:12px 16px;border-top:1px solid var(--border);font-size:14px;color:var(--text-primary);vertical-align:middle}
tbody tr{transition:background .15s}
tbody tr:nth-child(even){background:#f8fafc}
tbody tr:hover{background:#f0fdf4}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:8px;font-size:14px;font-weight:600;border:none;transition:all .2s;white-space:nowrap}
.btn-primary{background:var(--primary);color:#fff}
.btn-primary:hover{background:var(--primary-hover);box-shadow:0 4px 12px rgba(16,185,129,0.3)}
.btn-danger{background:var(--danger);color:#fff}
.btn-danger:hover{background:var(--danger-hover)}
.btn-secondary{background:#f1f5f9;color:var(--text-primary);border:1px solid var(--border)}
.btn-secondary:hover{background:#e2e8f0}
.btn-sm{padding:6px 12px;font-size:12px}
.btn-icon{padding:6px;border-radius:6px;border:none;background:none;color:var(--text-secondary);transition:all .15s;display:inline-flex;align-items:center;justify-content:center}
.btn-icon:hover{background:#f1f5f9;color:var(--text-primary)}
.btn-icon.danger:hover{background:#fef2f2;color:var(--danger)}

/* Badges */
.badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-confirmed{background:#dbeafe;color:#1e40af}
.badge-completed{background:#d1fae5;color:#065f46}
.badge-cancelled{background:#fee2e2;color:#991b1b}

/* Modal */
.modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:100;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;pointer-events:none;transition:opacity .25s}
.modal-overlay.open{opacity:1;pointer-events:all}
.modal{background:var(--card-bg);border-radius:var(--radius);box-shadow:var(--shadow-lg);width:100%;max-width:560px;max-height:90vh;overflow-y:auto;transform:translateY(20px);transition:transform .25s}
.modal-overlay.open .modal{transform:translateY(0)}
.modal-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal-header h3{font-size:18px;font-weight:700}
.modal-body{padding:24px}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:12px;justify-content:flex-end}
html[dir="rtl"] .modal-footer{justify-content:flex-start}

/* Forms */
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:6px}
.form-input,.form-select,.form-textarea{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:14px;color:var(--text-primary);background:#fff;transition:border-color .2s,box-shadow .2s;outline:none}
.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(16,185,129,0.1)}
.form-textarea{resize:vertical;min-height:80px}

/* Filter Bar */
.filter-bar{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:20px}
.filter-bar .form-select{width:auto;min-width:180px}

/* Page Header */
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
.page-header h2{font-size:22px;font-weight:700}

/* Department Grid */
.dept-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px}
.dept-card{padding:24px;text-align:center;transition:transform .2s,box-shadow .2s}
.dept-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg)}
.dept-card .dept-icon{width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(20,184,166,0.1));display:flex;align-items:center;justify-content:center;margin:0 auto 12px;font-size:24px}
.dept-card .dept-name{font-size:16px;font-weight:700;margin-bottom:4px}
.dept-card .dept-hospital{font-size:13px;color:var(--text-secondary);margin-bottom:8px}
.dept-card .dept-count{font-size:12px;color:var(--primary);font-weight:600}

/* Toast */
.toast-container{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:200;display:flex;flex-direction:column;gap:8px}
.toast{padding:12px 20px;border-radius:10px;font-size:14px;font-weight:500;color:#fff;box-shadow:var(--shadow-lg);animation:slideIn .3s ease,fadeOut .3s ease 2.7s}
.toast.success{background:var(--primary)}
.toast.error{background:var(--danger)}
@keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
@keyframes fadeOut{from{opacity:1}to{opacity:0}}

/* Section Header */
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.section-header h3{font-size:16px;font-weight:700}

/* Quick Actions */
.quick-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:24px}

/* Loading Spinner */
.spinner{display:inline-block;width:20px;height:20px;border:2px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .6s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}

/* Rating Stars */
.rating{display:inline-flex;align-items:center;gap:2px;color:var(--warning)}
.rating svg{width:14px;height:14px;fill:currentColor}

/* Empty State */
.empty-state{text-align:center;padding:60px 20px;color:var(--text-secondary)}
.empty-state svg{margin:0 auto 16px;opacity:.3}
.empty-state p{font-size:15px}

/* Responsive */
@media(max-width:768px){
  .sidebar{transform:translateX(100%)}
  html[dir="ltr"] .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .main{margin-right:0!important;margin-left:0!important}
  .hamburger{display:block}
  .stats-grid{grid-template-columns:1fr 1fr}
  .content{padding:16px}
  .page-header{flex-direction:column;align-items:flex-start}
  .dept-grid{grid-template-columns:1fr}
  .filter-bar{flex-direction:column;align-items:stretch}
  .filter-bar .form-select{width:100%}
}
@media(max-width:480px){
  .stats-grid{grid-template-columns:1fr}
}

/* Scrollbar */
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:#94a3b8}

/* Sidebar backdrop mobile */
.sidebar-backdrop{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.4);z-index:45}
.sidebar-backdrop.open{display:block}

/* Status actions */
.status-actions{display:flex;gap:4px;flex-wrap:wrap}
</style>
</head>
<body>
<!-- Sidebar Backdrop (mobile) -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo">
      <svg width="32" height="32" viewBox="0 0 32 32" fill="none"><rect width="32" height="32" rx="8" fill="url(#lg)"/><path d="M10 16h12M16 10v12" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/><defs><linearGradient id="lg" x1="0" y1="0" x2="32" y2="32"><stop stop-color="#10b981"/><stop offset="1" stop-color="#14b8a6"/></linearGradient></defs></svg>
      <span>MediCare Pro</span>
    </div>
    <div class="sidebar-subtitle" data-i18n="dashboard_subtitle">لوحة التحكم</div>
  </div>
  <nav class="sidebar-nav">
    <button class="nav-item active" data-page="dashboard" onclick="navigateTo('dashboard')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span data-i18n="nav_dashboard">لوحة التحكم</span>
    </button>
    <button class="nav-item" data-page="hospitals" onclick="navigateTo('hospitals')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg>
      <span data-i18n="nav_hospitals">المستشفيات</span>
    </button>
    <button class="nav-item" data-page="doctors" onclick="navigateTo('doctors')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <span data-i18n="nav_doctors">الأطباء</span>
    </button>
    <button class="nav-item" data-page="appointments" onclick="navigateTo('appointments')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <span data-i18n="nav_appointments">المواعيد</span>
    </button>
    <button class="nav-item" data-page="departments" onclick="navigateTo('departments')">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span data-i18n="nav_departments">الأقسام</span>
    </button>
  </nav>
  <div class="sidebar-footer">
    <div class="lang-toggle">
      <button class="lang-btn active" id="langAr" onclick="setLang('ar')">عربي</button>
      <button class="lang-btn" id="langEn" onclick="setLang('en')">English</button>
    </div>
  </div>
</aside>

<!-- Main -->
<div class="main">
  <header class="topbar">
    <div style="display:flex;align-items:center;gap:12px">
      <button class="hamburger" onclick="toggleSidebar()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="topbar-title" id="pageTitle" data-i18n="nav_dashboard">لوحة التحكم</div>
    </div>
    <div class="topbar-actions">
      <span style="font-size:13px;color:var(--text-secondary)" id="currentDate"></span>
    </div>
  </header>
  <main class="content" id="appContent">
    <div style="text-align:center;padding:60px"><div class="spinner" style="width:40px;height:40px;border-width:3px"></div></div>
  </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle"></h3>
      <button class="btn-icon" onclick="closeModal()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div class="modal-body" id="modalBody"></div>
    <div class="modal-footer" id="modalFooter"></div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
// ============================================================
// STATE
// ============================================================
let currentPage = 'dashboard';
let currentLang = 'ar';
let allHospitals = [];
let allDepartments = [];

const i18n = {
  ar: {
    dashboard_subtitle: 'لوحة التحكم',
    nav_dashboard: 'لوحة التحكم',
    nav_hospitals: 'المستشفيات',
    nav_doctors: 'الأطباء',
    nav_appointments: 'المواعيد',
    nav_departments: 'الأقسام',
    total_hospitals: 'إجمالي المستشفيات',
    total_doctors: 'إجمالي الأطباء',
    today_appointments: 'مواعيد اليوم',
    total_revenue: 'إجمالي الإيرادات',
    recent_appointments: 'أحدث المواعيد',
    quick_actions: 'إجراءات سريعة',
    add_hospital: 'إضافة مستشفى',
    edit_hospital: 'تعديل مستشفى',
    add_doctor: 'إضافة طبيب',
    edit_doctor: 'تعديل طبيب',
    name: 'الاسم',
    name_en: 'الاسم بالإنجليزية',
    location: 'الموقع',
    location_en: 'الموقع بالإنجليزية',
    phone: 'الهاتف',
    email: 'البريد الإلكتروني',
    rating: 'التقييم',
    specialty: 'التخصص',
    specialty_en: 'التخصص بالإنجليزية',
    experience: 'الخبرة (سنوات)',
    price: 'السعر',
    bio: 'الوصف',
    hospital: 'المستشفى',
    department: 'القسم',
    status: 'الحالة',
    actions: 'الإجراءات',
    save: 'حفظ',
    cancel: 'إلغاء',
    delete: 'حذف',
    edit: 'تعديل',
    confirm: 'تأكيد',
    complete: 'إكمال',
    pending: 'قيد الانتظار',
    confirmed: 'مؤكد',
    completed: 'مكتمل',
    cancelled: 'ملغي',
    patient_name: 'اسم المريض',
    patient_phone: 'هاتف المريض',
    date: 'التاريخ',
    time: 'الوقت',
    notes: 'ملاحظات',
    doctors_count: 'عدد الأطباء',
    departments_count: 'عدد الأقسام',
    no_data: 'لا توجد بيانات',
    filter_by_status: 'تصفية حسب الحالة',
    filter_by_hospital: 'تصفية حسب المستشفى',
    filter_by_department: 'تصفية حسب القسم',
    all: 'الكل',
    delete_confirm: 'هل أنت متأكد من الحذف؟',
    success_save: 'تم الحفظ بنجاح',
    success_delete: 'تم الحذف بنجاح',
    error: 'حدث خطأ',
    active: 'نشط',
    inactive: 'غير نشط',
    available: 'متاح',
    unavailable: 'غير متاح',
    currency: 'ر.س',
    years: 'سنوات',
    doctors: 'الأطباء',
    departments: 'الأقسام',
    manage_hospitals: 'إدارة المستشفيات',
    manage_doctors: 'إدارة الأطباء',
    manage_appointments: 'إدارة المواعيد',
    view_departments: 'عرض الأقسام',
  },
  en: {
    dashboard_subtitle: 'Admin Dashboard',
    nav_dashboard: 'Dashboard',
    nav_hospitals: 'Hospitals',
    nav_doctors: 'Doctors',
    nav_appointments: 'Appointments',
    nav_departments: 'Departments',
    total_hospitals: 'Total Hospitals',
    total_doctors: 'Total Doctors',
    today_appointments: "Today's Appointments",
    total_revenue: 'Total Revenue',
    recent_appointments: 'Recent Appointments',
    quick_actions: 'Quick Actions',
    add_hospital: 'Add Hospital',
    edit_hospital: 'Edit Hospital',
    add_doctor: 'Add Doctor',
    edit_doctor: 'Edit Doctor',
    name: 'Name',
    name_en: 'Name (English)',
    location: 'Location',
    location_en: 'Location (English)',
    phone: 'Phone',
    email: 'Email',
    rating: 'Rating',
    specialty: 'Specialty',
    specialty_en: 'Specialty (English)',
    experience: 'Experience (years)',
    price: 'Price',
    bio: 'Bio',
    hospital: 'Hospital',
    department: 'Department',
    status: 'Status',
    actions: 'Actions',
    save: 'Save',
    cancel: 'Cancel',
    delete: 'Delete',
    edit: 'Edit',
    confirm: 'Confirm',
    complete: 'Complete',
    pending: 'Pending',
    confirmed: 'Confirmed',
    completed: 'Completed',
    cancelled: 'Cancelled',
    patient_name: 'Patient Name',
    patient_phone: 'Patient Phone',
    date: 'Date',
    time: 'Time',
    notes: 'Notes',
    doctors_count: 'Doctors',
    departments_count: 'Departments',
    no_data: 'No data available',
    filter_by_status: 'Filter by status',
    filter_by_hospital: 'Filter by hospital',
    filter_by_department: 'Filter by department',
    all: 'All',
    delete_confirm: 'Are you sure you want to delete?',
    success_save: 'Saved successfully',
    success_delete: 'Deleted successfully',
    error: 'An error occurred',
    active: 'Active',
    inactive: 'Inactive',
    available: 'Available',
    unavailable: 'Unavailable',
    currency: 'SAR',
    years: 'years',
    doctors: 'Doctors',
    departments: 'Departments',
    manage_hospitals: 'Manage Hospitals',
    manage_doctors: 'Manage Doctors',
    manage_appointments: 'Manage Appointments',
    view_departments: 'View Departments',
  }
};

function t(key) { return i18n[currentLang][key] || key; }

// ============================================================
// HELPERS
// ============================================================
function apiFetch(url, options = {}) {
  return fetch(url, {
    headers: { 'Content-Type': 'application/json' },
    ...options,
  }).then(r => {
    if (!r.ok) return r.json().then(e => { throw new Error(e.error || 'Error'); });
    return r.json();
  });
}

function showToast(message, type = 'success') {
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = 'toast ' + type;
  toast.textContent = message;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}

function openModal(title, bodyHtml, footerHtml) {
  document.getElementById('modalTitle').textContent = title;
  document.getElementById('modalBody').innerHTML = bodyHtml;
  document.getElementById('modalFooter').innerHTML = footerHtml;
  document.getElementById('modalOverlay').classList.add('open');
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
}

function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarBackdrop').classList.toggle('open');
}

function ratingStars(rating) {
  const full = Math.floor(rating);
  let html = '';
  for (let i = 0; i < 5; i++) {
    html += '<svg viewBox="0 0 24 24" style="opacity:'+(i<full?1:0.3)+'"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
  }
  return '<span class="rating">' + html + '</span>';
}

function statusBadge(status) {
  const map = { pending: 'pending', confirmed: 'confirmed', completed: 'completed', cancelled: 'cancelled' };
  return '<span class="badge badge-' + (map[status]||'pending') + '">' + t(status) + '</span>';
}

// ============================================================
// LANGUAGE
// ============================================================
function setLang(lang) {
  currentLang = lang;
  document.documentElement.lang = lang;
  document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
  document.getElementById('langAr').classList.toggle('active', lang === 'ar');
  document.getElementById('langEn').classList.toggle('active', lang === 'en');
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (i18n[lang][key]) el.textContent = i18n[lang][key];
  });
  navigateTo(currentPage);
}

// ============================================================
// ROUTING
// ============================================================
function navigateTo(page) {
  currentPage = page;
  document.querySelectorAll('.nav-item').forEach(el => {
    el.classList.toggle('active', el.getAttribute('data-page') === page);
  });
  const titleMap = {
    dashboard: 'nav_dashboard',
    hospitals: 'nav_hospitals',
    doctors: 'nav_doctors',
    appointments: 'nav_appointments',
    departments: 'nav_departments',
  };
  const titleEl = document.getElementById('pageTitle');
  titleEl.textContent = t(titleMap[page] || 'nav_dashboard');
  titleEl.setAttribute('data-i18n', titleMap[page] || 'nav_dashboard');

  const content = document.getElementById('appContent');
  content.innerHTML = '<div style="text-align:center;padding:60px"><div class="spinner" style="width:40px;height:40px;border-width:3px"></div></div>';

  // Close sidebar on mobile
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarBackdrop').classList.remove('open');

  switch (page) {
    case 'dashboard': renderDashboard(); break;
    case 'hospitals': renderHospitals(); break;
    case 'doctors': renderDoctors(); break;
    case 'appointments': renderAppointments(); break;
    case 'departments': renderDepartments(); break;
  }
}

// ============================================================
// DASHBOARD PAGE
// ============================================================
async function renderDashboard() {
  const content = document.getElementById('appContent');
  try {
    const stats = await apiFetch('/api/dashboard/stats');
    let recentHtml = '';
    try {
      const appointments = await apiFetch('/api/appointments');
      const recent = appointments.slice(0, 5);
      if (recent.length) {
        recentHtml = '<div class="card" style="padding:20px;margin-top:24px"><div class="section-header"><h3>' + t('recent_appointments') + '</h3></div><div class="table-container"><table><thead><tr><th>' + t('patient_name') + '</th><th>' + t('nav_doctors') + '</th><th>' + t('date') + '</th><th>' + t('time') + '</th><th>' + t('status') + '</th></tr></thead><tbody>';
        recent.forEach(a => {
          recentHtml += '<tr><td>' + (a.patientName||'-') + '</td><td>' + (a.doctor?.name||'-') + '</td><td>' + (a.date||'-') + '</td><td>' + (a.time||'-') + '</td><td>' + statusBadge(a.status) + '</td></tr>';
        });
        recentHtml += '</tbody></table></div></div>';
      }
    } catch(e) {}

    content.innerHTML = \
      '<div class="stats-grid">' +
        '<div class="card stat-card"><div class="stat-icon green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg></div><div><div class="stat-value">' + stats.totalHospitals + '</div><div class="stat-label">' + t('total_hospitals') + '</div></div></div>' +
        '<div class="card stat-card"><div class="stat-icon teal"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div><div><div class="stat-value">' + stats.totalDoctors + '</div><div class="stat-label">' + t('total_doctors') + '</div></div></div>' +
        '<div class="card stat-card"><div class="stat-icon blue"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><div class="stat-value">' + stats.todayAppointments + '</div><div class="stat-label">' + t('today_appointments') + '</div></div></div>' +
        '<div class="card stat-card"><div class="stat-icon amber"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div><div class="stat-value">' + (stats.totalRevenue||0).toLocaleString() + '</div><div class="stat-label">' + t('total_revenue') + ' (' + t('currency') + ')</div></div></div>' +
      '</div>' +
      recentHtml +
      '<div class="quick-actions">' +
        '<button class="btn btn-primary" onclick="navigateTo(\'hospitals\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg> ' + t('manage_hospitals') + '</button>' +
        '<button class="btn btn-secondary" onclick="navigateTo(\'doctors\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> ' + t('manage_doctors') + '</button>' +
        '<button class="btn btn-secondary" onclick="navigateTo(\'appointments\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg> ' + t('manage_appointments') + '</button>' +
        '<button class="btn btn-secondary" onclick="navigateTo(\'departments\')"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg> ' + t('view_departments') + '</button>' +
      '</div>';
  } catch (e) {
    content.innerHTML = '<div class="empty-state"><p>' + t('error') + ': ' + e.message + '</p></div>';
  }
}

// ============================================================
// HOSPITALS PAGE
// ============================================================
async function renderHospitals() {
  const content = document.getElementById('appContent');
  try {
    allHospitals = await apiFetch('/api/hospitals');
    let html = '<div class="page-header"><h2>' + t('nav_hospitals') + '</h2><button class="btn btn-primary" onclick="openHospitalModal()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> ' + t('add_hospital') + '</button></div>';
    if (!allHospitals.length) {
      html += '<div class="card empty-state"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg><p>' + t('no_data') + '</p></div>';
    } else {
      html += '<div class="card"><div class="table-container"><table><thead><tr><th>' + t('name') + '</th><th>' + t('location') + '</th><th>' + t('phone') + '</th><th>' + t('rating') + '</th><th>' + t('departments') + '</th><th>' + t('doctors') + '</th><th>' + t('actions') + '</th></tr></thead><tbody>';
      allHospitals.forEach(h => {
        const name = currentLang === 'en' && h.nameEn ? h.nameEn : h.name;
        const loc = currentLang === 'en' && h.locationEn ? h.locationEn : h.location;
        html += '<tr><td><strong>' + name + '</strong></td><td>' + loc + '</td><td dir="ltr">' + (h.phone||'-') + '</td><td>' + ratingStars(h.rating) + '</td><td>' + (h._count?.departmentsList||0) + '</td><td>' + (h._count?.doctorsList||0) + '</td><td><div style="display:flex;gap:4px"><button class="btn-icon" onclick="openHospitalModal(\'' + h.id + '\')" title="' + t('edit') + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button><button class="btn-icon danger" onclick="deleteHospital(\'' + h.id + '\')" title="' + t('delete') + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></div></td></tr>';
      });
      html += '</tbody></table></div></div>';
    }
    content.innerHTML = html;
  } catch (e) {
    content.innerHTML = '<div class="empty-state"><p>' + t('error') + ': ' + e.message + '</p></div>';
  }
}

function openHospitalModal(id) {
  const h = id ? allHospitals.find(x => x.id === id) : null;
  const isEdit = !!h;
  const title = isEdit ? t('edit_hospital') : t('add_hospital');
  const body = \
    '<div class="form-group"><label class="form-label">' + t('name') + '</label><input class="form-input" id="f_name" value="' + (h?.name||'') + '" required></div>' +
    '<div class="form-group"><label class="form-label">' + t('name_en') + '</label><input class="form-input" id="f_nameEn" value="' + (h?.nameEn||'') + '"></div>' +
    '<div class="form-group"><label class="form-label">' + t('location') + '</label><input class="form-input" id="f_location" value="' + (h?.location||'') + '"></div>' +
    '<div class="form-group"><label class="form-label">' + t('location_en') + '</label><input class="form-input" id="f_locationEn" value="' + (h?.locationEn||'') + '"></div>' +
    '<div class="form-group"><label class="form-label">' + t('phone') + '</label><input class="form-input" id="f_phone" value="' + (h?.phone||'') + '" dir="ltr"></div>' +
    '<div class="form-group"><label class="form-label">' + t('email') + '</label><input class="form-input" id="f_email" type="email" value="' + (h?.email||'') + '" dir="ltr"></div>' +
    '<div class="form-group"><label class="form-label">' + t('rating') + '</label><input class="form-input" id="f_rating" type="number" min="0" max="5" step="0.1" value="' + (h?.rating||0) + '"></div>';
  const footer = \
    '<button class="btn btn-secondary" onclick="closeModal()">' + t('cancel') + '</button>' +
    '<button class="btn btn-primary" onclick="saveHospital(\'' + (id||'') + '\')">' + t('save') + '</button>';
  openModal(title, body, footer);
}

async function saveHospital(id) {
  const data = {
    name: document.getElementById('f_name').value,
    nameEn: document.getElementById('f_nameEn').value,
    location: document.getElementById('f_location').value,
    locationEn: document.getElementById('f_locationEn').value,
    phone: document.getElementById('f_phone').value,
    email: document.getElementById('f_email').value,
    rating: parseFloat(document.getElementById('f_rating').value) || 0,
  };
  try {
    if (id) {
      await apiFetch('/api/hospitals/' + id, { method: 'PUT', body: JSON.stringify(data) });
    } else {
      await apiFetch('/api/hospitals', { method: 'POST', body: JSON.stringify(data) });
    }
    closeModal();
    showToast(t('success_save'));
    renderHospitals();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

async function deleteHospital(id) {
  if (!confirm(t('delete_confirm'))) return;
  try {
    await apiFetch('/api/hospitals/' + id, { method: 'DELETE' });
    showToast(t('success_delete'));
    renderHospitals();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

// ============================================================
// DOCTORS PAGE
// ============================================================
async function renderDoctors() {
  const content = document.getElementById('appContent');
  try {
    // Ensure hospitals and departments are loaded for filters
    if (!allHospitals.length) allHospitals = await apiFetch('/api/hospitals');
    if (!allDepartments.length) allDepartments = await apiFetch('/api/departments');

    let filterHtml = '<div class="filter-bar">' +
      '<select class="form-select" id="filterHospital" onchange="renderDoctors()"><option value="">' + t('all') + ' - ' + t('hospital') + '</option>';
    allHospitals.forEach(h => {
      const name = currentLang === 'en' && h.nameEn ? h.nameEn : h.name;
      filterHtml += '<option value="' + h.id + '">' + name + '</option>';
    });
    filterHtml += '</select><select class="form-select" id="filterDept" onchange="renderDoctors()"><option value="">' + t('all') + ' - ' + t('department') + '</option>';
    allDepartments.forEach(d => {
      const name = currentLang === 'en' && d.nameEn ? d.nameEn : d.name;
      filterHtml += '<option value="' + d.id + '">' + name + '</option>';
    });
    filterHtml += '</select></div>';

    const hospitalId = document.getElementById('filterHospital')?.value || '';
    const departmentId = document.getElementById('filterDept')?.value || '';
    let query = '/api/doctors?';
    if (hospitalId) query += 'hospitalId=' + hospitalId + '&';
    if (departmentId) query += 'departmentId=' + departmentId + '&';
    const doctors = await apiFetch(query);

    let html = '<div class="page-header"><h2>' + t('nav_doctors') + '</h2><button class="btn btn-primary" onclick="openDoctorModal()"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> ' + t('add_doctor') + '</button></div>';
    html += filterHtml;

    if (!doctors.length) {
      html += '<div class="card empty-state"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><p>' + t('no_data') + '</p></div>';
    } else {
      html += '<div class="card"><div class="table-container"><table><thead><tr><th>' + t('name') + '</th><th>' + t('specialty') + '</th><th>' + t('hospital') + '</th><th>' + t('rating') + '</th><th>' + t('experience') + '</th><th>' + t('price') + '</th><th>' + t('status') + '</th><th>' + t('actions') + '</th></tr></thead><tbody>';
      doctors.forEach(d => {
        const name = currentLang === 'en' && d.nameEn ? d.nameEn : d.name;
        const spec = currentLang === 'en' && d.specialtyEn ? d.specialtyEn : d.specialty;
        const hName = currentLang === 'en' && d.hospital?.nameEn ? d.hospital.nameEn : d.hospital?.name;
        const statusText = d.available ? t('available') : t('unavailable');
        const statusClass = d.available ? 'badge-completed' : 'badge-cancelled';
        html += '<tr><td><strong>' + name + '</strong></td><td>' + spec + '</td><td>' + (hName||'-') + '</td><td>' + ratingStars(d.rating) + '</td><td>' + d.experience + ' ' + t('years') + '</td><td>' + d.price + ' ' + t('currency') + '</td><td><span class="badge ' + statusClass + '">' + statusText + '</span></td><td><div style="display:flex;gap:4px"><button class="btn-icon" onclick="openDoctorModal(\'' + d.id + '\')" title="' + t('edit') + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button><button class="btn-icon danger" onclick="deleteDoctor(\'' + d.id + '\')" title="' + t('delete') + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button></div></td></tr>';
      });
      html += '</tbody></table></div></div>';
    }
    content.innerHTML = html;
  } catch (e) {
    content.innerHTML = '<div class="empty-state"><p>' + t('error') + ': ' + e.message + '</p></div>';
  }
}

function openDoctorModal(id) {
  const doctors = document.querySelectorAll('table tbody tr');
  // We need the full doctor list, fetch it
  apiFetch('/api/doctors').then(allDocs => {
    const d = id ? allDocs.find(x => x.id === id) : null;
    const isEdit = !!d;
    const title = isEdit ? t('edit_doctor') : t('add_doctor');

    let hospitalOpts = '<option value="">-- ' + t('hospital') + ' --</option>';
    allHospitals.forEach(h => {
      const name = currentLang === 'en' && h.nameEn ? h.nameEn : h.name;
      const sel = d && d.hospitalId === h.id ? ' selected' : '';
      hospitalOpts += '<option value="' + h.id + '"' + sel + '>' + name + '</option>';
    });

    let deptOpts = '<option value="">-- ' + t('department') + ' --</option>';
    allDepartments.forEach(dept => {
      const name = currentLang === 'en' && dept.nameEn ? dept.nameEn : dept.name;
      const sel = d && d.departmentId === dept.id ? ' selected' : '';
      deptOpts += '<option value="' + dept.id + '"' + sel + '>' + name + '</option>';
    });

    const body = \
      '<div class="form-group"><label class="form-label">' + t('name') + '</label><input class="form-input" id="f_name" value="' + (d?.name||'') + '" required></div>' +
      '<div class="form-group"><label class="form-label">' + t('name_en') + '</label><input class="form-input" id="f_nameEn" value="' + (d?.nameEn||'') + '"></div>' +
      '<div class="form-group"><label class="form-label">' + t('specialty') + '</label><input class="form-input" id="f_specialty" value="' + (d?.specialty||'') + '" required></div>' +
      '<div class="form-group"><label class="form-label">' + t('specialty_en') + '</label><input class="form-input" id="f_specialtyEn" value="' + (d?.specialtyEn||'') + '"></div>' +
      '<div class="form-group"><label class="form-label">' + t('hospital') + '</label><select class="form-select" id="f_hospitalId">' + hospitalOpts + '</select></div>' +
      '<div class="form-group"><label class="form-label">' + t('department') + '</label><select class="form-select" id="f_departmentId">' + deptOpts + '</select></div>' +
      '<div class="form-group"><label class="form-label">' + t('phone') + '</label><input class="form-input" id="f_phone" value="' + (d?.phone||'') + '" dir="ltr"></div>' +
      '<div class="form-group"><label class="form-label">' + t('email') + '</label><input class="form-input" id="f_email" type="email" value="' + (d?.email||'') + '" dir="ltr"></div>' +
      '<div class="form-group"><label class="form-label">' + t('experience') + '</label><input class="form-input" id="f_experience" type="number" min="0" value="' + (d?.experience||0) + '"></div>' +
      '<div class="form-group"><label class="form-label">' + t('price') + '</label><input class="form-input" id="f_price" type="number" min="0" step="0.01" value="' + (d?.price||0) + '"></div>' +
      '<div class="form-group"><label class="form-label">' + t('rating') + '</label><input class="form-input" id="f_rating" type="number" min="0" max="5" step="0.1" value="' + (d?.rating||0) + '"></div>' +
      '<div class="form-group"><label class="form-label">' + t('bio') + '</label><textarea class="form-textarea" id="f_bio">' + (d?.bio||'') + '</textarea></div>' +
      '<div class="form-group"><label class="form-label">' + t('status') + '</label><select class="form-select" id="f_available"><option value="true"' + (d?.available !== false ? ' selected' : '') + '>' + t('available') + '</option><option value="false"' + (d?.available === false ? ' selected' : '') + '>' + t('unavailable') + '</option></select></div>';

    const footer = \
      '<button class="btn btn-secondary" onclick="closeModal()">' + t('cancel') + '</button>' +
      '<button class="btn btn-primary" onclick="saveDoctor(\'' + (id||'') + '\')">' + t('save') + '</button>';
    openModal(title, body, footer);
  });
}

async function saveDoctor(id) {
  const data = {
    name: document.getElementById('f_name').value,
    nameEn: document.getElementById('f_nameEn').value,
    specialty: document.getElementById('f_specialty').value,
    specialtyEn: document.getElementById('f_specialtyEn').value,
    hospitalId: document.getElementById('f_hospitalId').value,
    departmentId: document.getElementById('f_departmentId').value,
    phone: document.getElementById('f_phone').value,
    email: document.getElementById('f_email').value,
    experience: parseInt(document.getElementById('f_experience').value) || 0,
    price: parseFloat(document.getElementById('f_price').value) || 0,
    rating: parseFloat(document.getElementById('f_rating').value) || 0,
    bio: document.getElementById('f_bio').value,
    available: document.getElementById('f_available').value === 'true',
  };
  try {
    if (id) {
      await apiFetch('/api/doctors/' + id, { method: 'PUT', body: JSON.stringify(data) });
    } else {
      await apiFetch('/api/doctors', { method: 'POST', body: JSON.stringify(data) });
    }
    closeModal();
    showToast(t('success_save'));
    renderDoctors();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

async function deleteDoctor(id) {
  if (!confirm(t('delete_confirm'))) return;
  try {
    await apiFetch('/api/doctors/' + id, { method: 'DELETE' });
    showToast(t('success_delete'));
    renderDoctors();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

// ============================================================
// APPOINTMENTS PAGE
// ============================================================
async function renderAppointments() {
  const content = document.getElementById('appContent');
  try {
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    let query = '/api/appointments';
    if (statusFilter) query += '?status=' + statusFilter;
    const appointments = await apiFetch(query);

    let html = '<div class="page-header"><h2>' + t('nav_appointments') + '</h2></div>';
    html += '<div class="filter-bar"><select class="form-select" id="filterStatus" onchange="renderAppointments()"><option value="">' + t('all') + ' - ' + t('status') + '</option><option value="pending"' + (statusFilter==='pending'?' selected':'') + '>' + t('pending') + '</option><option value="confirmed"' + (statusFilter==='confirmed'?' selected':'') + '>' + t('confirmed') + '</option><option value="completed"' + (statusFilter==='completed'?' selected':'') + '>' + t('completed') + '</option><option value="cancelled"' + (statusFilter==='cancelled'?' selected':'') + '>' + t('cancelled') + '</option></select></div>';

    if (!appointments.length) {
      html += '<div class="card empty-state"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><p>' + t('no_data') + '</p></div>';
    } else {
      html += '<div class="card"><div class="table-container"><table><thead><tr><th>' + t('patient_name') + '</th><th>' + t('nav_doctors') + '</th><th>' + t('hospital') + '</th><th>' + t('date') + '</th><th>' + t('time') + '</th><th>' + t('status') + '</th><th>' + t('actions') + '</th></tr></thead><tbody>';
      appointments.forEach(a => {
        const docName = a.doctor ? (currentLang === 'en' && a.doctor.nameEn ? a.doctor.nameEn : a.doctor.name) : '-';
        const hospName = a.doctor?.hospital ? (currentLang === 'en' && a.doctor.hospital.nameEn ? a.doctor.hospital.nameEn : a.doctor.hospital.name) : '-';
        let actions = '';
        if (a.status === 'pending') {
          actions += '<button class="btn btn-sm btn-primary" onclick="updateAppointmentStatus(\'' + a.id + '\',\'confirmed\')">' + t('confirm') + '</button>';
          actions += '<button class="btn btn-sm btn-secondary" onclick="updateAppointmentStatus(\'' + a.id + '\',\'cancelled\')">' + t('cancel') + '</button>';
        } else if (a.status === 'confirmed') {
          actions += '<button class="btn btn-sm btn-primary" onclick="updateAppointmentStatus(\'' + a.id + '\',\'completed\')">' + t('complete') + '</button>';
          actions += '<button class="btn btn-sm btn-secondary" onclick="updateAppointmentStatus(\'' + a.id + '\',\'cancelled\')">' + t('cancel') + '</button>';
        }
        actions += ' <button class="btn-icon danger" onclick="deleteAppointment(\'' + a.id + '\')" title="' + t('delete') + '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>';
        html += '<tr><td>' + (a.patientName||'-') + '</td><td>' + docName + '</td><td>' + hospName + '</td><td>' + (a.date||'-') + '</td><td dir="ltr">' + (a.time||'-') + '</td><td>' + statusBadge(a.status) + '</td><td><div class="status-actions">' + actions + '</div></td></tr>';
      });
      html += '</tbody></table></div></div>';
    }
    content.innerHTML = html;
  } catch (e) {
    content.innerHTML = '<div class="empty-state"><p>' + t('error') + ': ' + e.message + '</p></div>';
  }
}

async function updateAppointmentStatus(id, status) {
  try {
    await apiFetch('/api/appointments/' + id, { method: 'PUT', body: JSON.stringify({ status }) });
    showToast(t('success_save'));
    renderAppointments();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

async function deleteAppointment(id) {
  if (!confirm(t('delete_confirm'))) return;
  try {
    await apiFetch('/api/appointments/' + id, { method: 'DELETE' });
    showToast(t('success_delete'));
    renderAppointments();
  } catch (e) {
    showToast(e.message, 'error');
  }
}

// ============================================================
// DEPARTMENTS PAGE
// ============================================================
async function renderDepartments() {
  const content = document.getElementById('appContent');
  try {
    allDepartments = await apiFetch('/api/departments');
    let html = '<div class="page-header"><h2>' + t('nav_departments') + '</h2></div>';

    if (!allDepartments.length) {
      html += '<div class="card empty-state"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg><p>' + t('no_data') + '</p></div>';
    } else {
      html += '<div class="dept-grid">';
      allDepartments.forEach(d => {
        const name = currentLang === 'en' && d.nameEn ? d.nameEn : d.name;
        const hName = d.hospital ? (currentLang === 'en' && d.hospital.nameEn ? d.hospital.nameEn : d.hospital.name) : '-';
        const iconEmoji = d.icon || '🏥';
        html += '<div class="card dept-card"><div class="dept-icon">' + iconEmoji + '</div><div class="dept-name">' + name + '</div><div class="dept-hospital">' + hName + '</div><div class="dept-count">' + (d._count?.doctors||0) + ' ' + t('doctors') + '</div></div>';
      });
      html += '</div>';
    }
    content.innerHTML = html;
  } catch (e) {
    content.innerHTML = '<div class="empty-state"><p>' + t('error') + ': ' + e.message + '</p></div>';
  }
}

// ============================================================
// INIT
// ============================================================
(function init() {
  // Set current date
  const now = new Date();
  const dateStr = now.toLocaleDateString(currentLang === 'ar' ? 'ar-SA' : 'en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
  });
  document.getElementById('currentDate').textContent = dateStr;

  // Load hospitals/departments cache
  apiFetch('/api/hospitals').then(d => { allHospitals = d; }).catch(() => {});
  apiFetch('/api/departments').then(d => { allDepartments = d; }).catch(() => {});

  // Render dashboard
  navigateTo('dashboard');
})();
</script>
</body>
</html>`;

// ============================================================
// SERVE HTML AT ROOT
// ============================================================
app.get('/', (c) => c.html(HTML))

// ============================================================
// START SERVER
// ============================================================
console.log('🏥 MediCare Pro Admin Dashboard running on http://localhost:3001')

const server = Bun.serve({
  port: 3001,
  fetch: app.fetch,
})

// Keep process alive
process.on('SIGINT', () => {
  console.log('Shutting down...')
  server.stop()
  process.exit(0)
})

// Prevent process from exiting
setInterval(() => {}, 60000)
