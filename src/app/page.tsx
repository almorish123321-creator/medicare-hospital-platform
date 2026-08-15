'use client'

import { useState } from 'react'
import {
  projectInfo,
  apiEndpoints,
  dbTables,
  setupSteps,
  demoCredentials,
  type ApiEndpoint,
} from '@/lib/project-data'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Separator } from '@/components/ui/separator'
import {
  Copy,
  CheckCircle2,
  Server,
  Database,
  Key,
  Globe,
  Shield,
  Activity,
  FileText,
  Layers,
  Users,
  Bell,
  CreditCard,
  BarChart3,
  Clock,
  ChevronRight,
  ExternalLink,
  Play,
  Download,
  Zap,
  TestTube,
  Smartphone,
  Terminal,
  GitBranch,
} from 'lucide-react'

const methodColors: Record<string, string> = {
  GET: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
  POST: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
  PUT: 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
  DELETE: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
  PATCH: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
}

const categoryLabels: Record<string, string> = {
  public: '🌐 عام | Public',
  auth: '🔐 مصادقة | Auth',
  patient: '🤒 مريض | Patient',
  doctor: '👨‍⚕️ طبيب | Doctor',
  receptionist: '📋 استقبال | Receptionist',
  nurse: '👩‍⚕️ تمريض | Nurse',
  pharmacist: '💊 صيدلية | Pharmacist',
  admin: '🏥 مدير المستشفى | Admin',
  superAdmin: '👑 المدير العام | Super Admin',
}

export default function Home() {
  const [copiedIdx, setCopiedIdx] = useState<number | null>(null)
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedCategory, setSelectedCategory] = useState<string>('all')
  const [baseUrl, setBaseUrl] = useState('http://localhost:8000')

  const allEndpoints = Object.entries(apiEndpoints).flatMap(([cat, eps]) =>
    eps.map((ep) => ({ ...ep, category: cat }))
  )

  const filteredEndpoints = allEndpoints.filter((ep) => {
    const matchesSearch =
      !searchQuery ||
      ep.path.toLowerCase().includes(searchQuery.toLowerCase()) ||
      ep.description_en.toLowerCase().includes(searchQuery.toLowerCase()) ||
      ep.description_ar.includes(searchQuery)
    const matchesCategory = selectedCategory === 'all' || ep.category === selectedCategory
    return matchesSearch && matchesCategory
  })

  const totalEndpoints = allEndpoints.length

  const copyToClipboard = (text: string, idx: number) => {
    navigator.clipboard.writeText(text)
    setCopiedIdx(idx)
    setTimeout(() => setCopiedIdx(null), 2000)
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
      {/* Hero Header */}
      <header className="relative overflow-hidden border-b border-slate-200 dark:border-slate-800">
        <div className="absolute inset-0 bg-gradient-to-r from-teal-600/10 via-emerald-500/5 to-cyan-600/10 dark:from-teal-600/20 dark:via-emerald-500/10 dark:to-cyan-600/20" />
        <div className="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="space-y-2">
              <div className="flex items-center gap-3">
                <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-2xl shadow-lg shadow-teal-500/25">
                  🏥
                </div>
                <div>
                  <h1 className="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    {projectInfo.name}
                  </h1>
                  <p className="text-sm text-slate-500 dark:text-slate-400">
                    {projectInfo.subtitle}
                  </p>
                </div>
              </div>
              <p className="max-w-xl text-sm leading-relaxed text-slate-600 dark:text-slate-400" dir="rtl">
                {projectInfo.description_ar}
              </p>
            </div>
            <div className="flex flex-wrap gap-2">
              <Badge variant="secondary" className="gap-1 bg-teal-50 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300">
                <GitBranch className="h-3 w-3" /> v{projectInfo.version}
              </Badge>
              <Badge variant="secondary" className="gap-1 bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                <Server className="h-3 w-3" /> Laravel {projectInfo.laravel}
              </Badge>
              <Badge variant="secondary" className="gap-1 bg-violet-50 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300">
                <Terminal className="h-3 w-3" /> PHP {projectInfo.php}
              </Badge>
              <Badge variant="secondary" className="gap-1 bg-amber-50 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                <Layers className="h-3 w-3" /> {projectInfo.stats.totalPhpFiles} ملف PHP
              </Badge>
              <Badge variant="secondary" className="gap-1 bg-rose-50 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300">
                <Globe className="h-3 w-3" /> AR + EN
              </Badge>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <Tabs defaultValue="overview" className="space-y-6">
          <TabsList className="grid w-full grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-1 h-auto p-1">
            <TabsTrigger value="overview" className="gap-1 text-xs sm:text-sm py-2">
              <Activity className="h-3.5 w-3.5" />
              <span className="hidden sm:inline">نظرة عامة</span>
              <span className="sm:hidden">نظرة</span>
            </TabsTrigger>
            <TabsTrigger value="api" className="gap-1 text-xs sm:text-sm py-2">
              <Layers className="h-3.5 w-3.5" />
              <span>API</span>
            </TabsTrigger>
            <TabsTrigger value="database" className="gap-1 text-xs sm:text-sm py-2">
              <Database className="h-3.5 w-3.5" />
              <span>قاعدة البيانات</span>
            </TabsTrigger>
            <TabsTrigger value="setup" className="gap-1 text-xs sm:text-sm py-2">
              <Play className="h-3.5 w-3.5" />
              <span>التشغيل</span>
            </TabsTrigger>
            <TabsTrigger value="tester" className="gap-1 text-xs sm:text-sm py-2">
              <TestTube className="h-3.5 w-3.5" />
              <span>اختبار</span>
            </TabsTrigger>
            <TabsTrigger value="mobile" className="gap-1 text-xs sm:text-sm py-2">
              <Smartphone className="h-3.5 w-3.5" />
              <span>موبايل</span>
            </TabsTrigger>
          </TabsList>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* OVERVIEW TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="overview" className="space-y-6">
            {/* Stats Grid */}
            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
              {[
                { label: 'API Endpoints', value: totalEndpoints, icon: Layers, color: 'from-teal-500 to-emerald-600' },
                { label: 'Database Tables', value: projectInfo.stats.migrations, icon: Database, color: 'from-blue-500 to-indigo-600' },
                { label: 'Models', value: projectInfo.stats.models, icon: FileText, color: 'from-violet-500 to-purple-600' },
                { label: 'Controllers', value: projectInfo.stats.controllers, icon: Server, color: 'from-amber-500 to-orange-600' },
                { label: 'Tests', value: projectInfo.stats.tests, icon: TestTube, color: 'from-rose-500 to-pink-600' },
                { label: 'Services', value: projectInfo.stats.services, icon: Zap, color: 'from-cyan-500 to-sky-600' },
                { label: 'Jobs', value: projectInfo.stats.jobs, icon: Clock, color: 'from-lime-500 to-green-600' },
                { label: 'Events', value: projectInfo.stats.events, icon: Bell, color: 'from-fuchsia-500 to-pink-600' },
                { label: 'Seeders', value: projectInfo.stats.seeders, icon: Play, color: 'from-emerald-500 to-teal-600' },
                { label: 'Roles', value: projectInfo.roles.length, icon: Shield, color: 'from-red-500 to-rose-600' },
              ].map((stat) => (
                <Card key={stat.label} className="relative overflow-hidden">
                  <div className={`absolute top-0 right-0 h-16 w-16 -translate-y-4 translate-x-4 rounded-full bg-gradient-to-br ${stat.color} opacity-10`} />
                  <CardContent className="p-4">
                    <div className="flex items-center gap-2">
                      <stat.icon className="h-4 w-4 text-slate-400" />
                      <span className="text-xs font-medium text-slate-500 dark:text-slate-400">{stat.label}</span>
                    </div>
                    <p className="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{stat.value}</p>
                  </CardContent>
                </Card>
              ))}
            </div>

            {/* Tech Stack + Roles */}
            <div className="grid gap-6 lg:grid-cols-2">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Layers className="h-5 w-5 text-teal-500" />
                    التقنيات المستخدمة | Tech Stack
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-2 gap-2 sm:grid-cols-3">
                    {projectInfo.techStack.map((tech) => (
                      <div key={tech.name} className="flex items-center gap-2 rounded-lg border p-2.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <span className="text-lg">{tech.icon}</span>
                        <div>
                          <p className="text-xs font-semibold text-slate-900 dark:text-white">{tech.name}</p>
                          <p className="text-[10px] text-slate-500">{tech.desc}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Users className="h-5 w-5 text-teal-500" />
                    أدوار المستخدمين | User Roles
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-2">
                    {projectInfo.roles.map((role) => {
                      const count = allEndpoints.filter((ep) =>
                        ep.roles.includes(role.name) || ep.roles.includes('all')
                      ).length
                      return (
                        <div
                          key={role.name}
                          className="flex items-center justify-between rounded-lg border p-2.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50"
                        >
                          <div className="flex items-center gap-2.5">
                            <span className="text-lg">{role.icon}</span>
                            <div>
                              <p className="text-sm font-semibold text-slate-900 dark:text-white">
                                {role.name_ar}
                              </p>
                              <p className="text-[10px] text-slate-500">{role.name}</p>
                            </div>
                          </div>
                          <Badge variant="outline" className="text-xs">
                            {count} API
                          </Badge>
                        </div>
                      )
                    })}
                  </div>
                </CardContent>
              </Card>
            </div>

            {/* Features */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Zap className="h-5 w-5 text-teal-500" />
                  مميزات النظام | System Features
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                  {[
                    { icon: Globe, title: 'ثنائي اللغة', desc: 'عربي (RTL) + إنجليزي (LTR) مع دعم كامل', titleEn: 'Bilingual', descEn: 'Arabic (RTL) + English (LTR) full support' },
                    { icon: Users, title: '7 أدوار مستخدمين', desc: 'تحكم كامل بالصلاحيات لكل دور', titleEn: '7 User Roles', descEn: 'Complete permission control per role' },
                    { icon: Clock, title: 'نظام طوابير ذكي', desc: 'D{dept}-{seq} مع إعادة تعيين يومي', titleEn: 'Smart Queue', descEn: 'D{dept}-{seq} with daily reset' },
                    { icon: Bell, title: 'إشعارات فورية', desc: 'FCM Push + SMS + Database Notifications', titleEn: 'Real-time', descEn: 'FCM Push + SMS + DB Notifications' },
                    { icon: CreditCard, title: 'دفع إلكتروني', desc: 'كاش + بطاقة + محفظة + تأمين', titleEn: 'E-Payments', descEn: 'Cash + Card + Wallet + Insurance' },
                    { icon: BarChart3, title: 'تقارير متقدمة', desc: 'PDF + Excel مع لوحات تحكم تحليلية', titleEn: 'Reports', descEn: 'PDF + Excel with analytics dashboards' },
                    { icon: Shield, title: 'أمان متقدم', desc: 'Sanctum Auth + Rate Limiting + Multi-tenancy', titleEn: 'Security', descEn: 'Sanctum Auth + Rate Limiting + Multi-tenancy' },
                    { icon: FileText, title: 'Swagger/OpenAPI', desc: 'توثيق تلقائي لجميع الـ 80+ endpoint', titleEn: 'Swagger Docs', descEn: 'Auto documentation for 80+ endpoints' },
                    { icon: Database, title: '18 جدول', desc: 'MySQL 8 مع Soft Deletes و Relations كاملة', titleEn: '18 Tables', descEn: 'MySQL 8 with Soft Deletes & full relations' },
                  ].map((f) => (
                    <div key={f.titleEn} className="flex items-start gap-3 rounded-lg border p-3">
                      <f.icon className="mt-0.5 h-4 w-4 shrink-0 text-teal-500" />
                      <div>
                        <p className="text-sm font-semibold text-slate-900 dark:text-white">{f.title}</p>
                        <p className="text-xs text-slate-500">{f.desc}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Project Structure */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <FileText className="h-5 w-5 text-teal-500" />
                  هيكل المشروع | Project Structure
                </CardTitle>
              </CardHeader>
              <CardContent>
                <ScrollArea className="h-80">
                  <pre className="text-xs leading-relaxed text-slate-600 dark:text-slate-400" dir="ltr">
{`medicare-pro/
├── app/
│   ├── Console/
│   │   ├── Commands/        (6 Artisan Commands)
│   │   └── Kernel.php       (Task Scheduler)
│   ├── Events/              (5 Events)
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/Api/  (31 Controllers)
│   │   │   ├── Admin/       (6: Dashboard, Doctor, Dept, Report, Setting, Staff)
│   │   │   ├── Doctor/      (5: Appointment, Dashboard, MedicalRecord, Prescription, Schedule)
│   │   │   ├── Nurse/       (1: VitalSign)
│   │   │   ├── Patient/     (7: Appointment, Invoice, MedicalRecord, Notification, Prescription, Profile, Queue)
│   │   │   ├── Pharmacist/  (2: Medication, Prescription)
│   │   │   ├── Receptionist/(4: Appointment, Dashboard, Patient, Queue)
│   │   │   ├── SuperAdmin/  (4: Analytics, Hospital, Language, Plan)
│   │   │   ├── AuthController.php
│   │   │   └── PublicController.php
│   │   ├── Middleware/       (3: SetLocale, CheckRole, CheckHospitalAccess)
│   │   ├── Requests/        (24 FormRequests by role)
│   │   └── Resources/        (17 API Resources)
│   ├── Jobs/                (9 Queued Jobs)
│   ├── Listeners/           (5 Event Listeners)
│   ├── Models/              (18 Eloquent Models)
│   ├── Providers/           (AppServiceProvider)
│   ├── Repositories/        (6 Interfaces + 6 Implementations + Base)
│   ├── Services/            (6: Queue, Notification, Payment, PDF, SMS, Translation)
│   ├── Swagger/             (swagger.php)
│   └── Traits/              (3: HasHospitalAccess, LogsActivity, Translatable)
├── config/                  (13 Config Files)
├── database/
│   ├── factories/           (13 Model Factories)
│   ├── migrations/          (22 Migrations)
│   └── seeders/             (10 Seeders)
├── docker/                  (nginx, php, mysql, soketi configs)
├── resources/
│   ├── lang/ar/             (9 Arabic language files)
│   ├── lang/en/             (9 English language files)
│   └── views/emails/        (2 Email templates)
├── routes/
│   ├── api.php              (80+ API endpoints)
│   ├── web.php
│   └── console.php
├── tests/
│   ├── Feature/             (10 Feature Tests)
│   └── Unit/                (3 Unit Tests)
├── Dockerfile
├── docker-compose.yml       (8 Services)
├── docker-entrypoint.sh
├── .env.example             (Complete config)
├── composer.json
└── README.md                (Bilingual)`}
                  </pre>
                </ScrollArea>
              </CardContent>
            </Card>
          </TabsContent>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* API DOCUMENTATION TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="api" className="space-y-4">
            {/* Search + Filter */}
            <Card>
              <CardContent className="p-4">
                <div className="flex flex-col gap-3 sm:flex-row">
                  <div className="relative flex-1">
                    <Input
                      placeholder="ابحث عن endpoint... (path, description)"
                      value={searchQuery}
                      onChange={(e) => setSearchQuery(e.target.value)}
                      className="pr-10"
                    />
                    <Layers className="absolute top-1/2 right-3 h-4 w-4 -translate-y-1/2 text-slate-400" />
                  </div>
                  <div className="flex flex-wrap gap-1.5">
                    <Button
                      size="sm"
                      variant={selectedCategory === 'all' ? 'default' : 'outline'}
                      onClick={() => setSelectedCategory('all')}
                    >
                      الكل ({totalEndpoints})
                    </Button>
                    {Object.entries(apiEndpoints).map(([key, eps]) => (
                      <Button
                        key={key}
                        size="sm"
                        variant={selectedCategory === key ? 'default' : 'outline'}
                        onClick={() => setSelectedCategory(key)}
                      >
                        {eps.length}
                      </Button>
                    ))}
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Base URL Setting */}
            <Card>
              <CardContent className="flex items-center gap-3 p-3">
                <Server className="h-4 w-4 text-slate-400" />
                <span className="text-sm font-medium text-slate-600 dark:text-slate-400">Base URL:</span>
                <Input
                  value={baseUrl}
                  onChange={(e) => setBaseUrl(e.target.value)}
                  className="max-w-sm font-mono text-xs"
                />
                <Button
                  size="sm"
                  variant="outline"
                  onClick={() => copyToClipboard(baseUrl, -1)}
                >
                  {copiedIdx === -1 ? <CheckCircle2 className="h-3 w-3" /> : <Copy className="h-3 w-3" />}
                </Button>
              </CardContent>
            </Card>

            {/* Results Count */}
            <p className="text-sm text-slate-500">
              عرض {filteredEndpoints.length} من {totalEndpoints} endpoint
            </p>

            {/* Endpoints by Category */}
            {Object.entries(apiEndpoints)
              .filter(([key]) => selectedCategory === 'all' || key === selectedCategory)
              .map(([category, endpoints]) => {
                const filtered = endpoints.filter((ep) => {
                  const matchesSearch =
                    !searchQuery ||
                    ep.path.toLowerCase().includes(searchQuery.toLowerCase()) ||
                    ep.description_en.toLowerCase().includes(searchQuery.toLowerCase()) ||
                    ep.description_ar.includes(searchQuery)
                  return matchesSearch
                })
                if (filtered.length === 0) return null
                return (
                  <Card key={category}>
                    <CardHeader className="pb-2">
                      <CardTitle className="text-base">{categoryLabels[category]}</CardTitle>
                      <CardDescription>{filtered.length} endpoints</CardDescription>
                    </CardHeader>
                    <CardContent className="space-y-1">
                      {filtered.map((ep, idx) => (
                        <EndpointRow
                          key={ep.method + ep.path}
                          endpoint={ep}
                          baseUrl={baseUrl}
                          globalIdx={idx}
                          copiedIdx={copiedIdx}
                          onCopy={copyToClipboard}
                        />
                      ))}
                    </CardContent>
                  </Card>
                )
              })}
          </TabsContent>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* DATABASE TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="database" className="space-y-4">
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {dbTables.map((table) => (
                <Card key={table.name} className="overflow-hidden">
                  <CardHeader className="bg-gradient-to-r from-teal-500/10 to-emerald-500/10 p-3 pb-2">
                    <CardTitle className="flex items-center gap-2 text-sm">
                      <Database className="h-4 w-4 text-teal-500" />
                      <span dir="ltr">{table.name}</span>
                    </CardTitle>
                    <CardDescription className="text-xs" dir="rtl">
                      {table.name_ar}
                    </CardDescription>
                  </CardHeader>
                  <CardContent className="p-3">
                    <div className="space-y-1.5">
                      {table.columns.map((col) => (
                        <div
                          key={col.name}
                          className="flex items-center justify-between rounded border px-2 py-1.5 text-xs"
                        >
                          <div className="flex items-center gap-2">
                            <span className="font-mono font-semibold text-slate-900 dark:text-white">
                              {col.name}
                            </span>
                            <Badge variant="outline" className="text-[10px] px-1 py-0">
                              {col.type}
                            </Badge>
                            {col.nullable && (
                              <Badge variant="secondary" className="text-[10px] px-1 py-0">
                                nullable
                              </Badge>
                            )}
                          </div>
                          <span className="text-[10px] text-slate-500 hidden sm:inline">
                            {col.description_en}
                          </span>
                        </div>
                      ))}
                    </div>
                    {table.relations && (
                      <div className="mt-2 rounded bg-slate-50 p-2 dark:bg-slate-800/50">
                        <p className="text-[10px] font-medium text-slate-500">Relations:</p>
                        <p className="text-[10px] text-slate-600 dark:text-slate-400 font-mono" dir="ltr">
                          {table.relations}
                        </p>
                      </div>
                    )}
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* SETUP TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="setup" className="space-y-6">
            {/* Quick Docker Start */}
            <Card className="border-teal-200 bg-gradient-to-r from-teal-50 to-emerald-50 dark:border-teal-800 dark:from-teal-950/50 dark:to-emerald-950/50">
              <CardHeader>
                <CardTitle className="flex items-center gap-2 text-teal-800 dark:text-teal-300">
                  <Zap className="h-5 w-5" />
                  تشغيل سريع بـ Docker | Quick Docker Start
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <CodeBlock
                  code={`# 1. Extract and enter project directory\nunzip medicare-pro.zip\ncd medicare-pro\n\n# 2. Copy environment file\ncp .env.example .env\n\n# 3. Start all services (MySQL, Redis, Nginx, Soketi, PHP-FPM)\ndocker-compose up -d --build\n\n# 4. Run migrations and seed\ndocker-compose exec app php artisan migrate --seed\n\n# 5. Generate Swagger docs\ndocker-compose exec app php artisan l5-swagger:generate\n\n# ✅ App is running at http://localhost`}
                />
                <div className="flex gap-2">
                  <Badge variant="outline" className="gap-1 text-xs">
                    <Server className="h-3 w-3" /> MySQL: localhost:3306
                  </Badge>
                  <Badge variant="outline" className="gap-1 text-xs">
                    <Activity className="h-3 w-3" /> Redis: localhost:6379
                  </Badge>
                  <Badge variant="outline" className="gap-1 text-xs">
                    <ExternalLink className="h-3 w-3" /> phpMyAdmin: localhost:8080
                  </Badge>
                  <Badge variant="outline" className="gap-1 text-xs">
                    <Zap className="h-3 w-3" /> Soketi WS: localhost:6001
                  </Badge>
                </div>
              </CardContent>
            </Card>

            {/* Step-by-step Manual Setup */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Terminal className="h-5 w-5 text-teal-500" />
                  إعداد يدوي | Manual Setup
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {setupSteps.map((step) => (
                    <div key={step.step} className="flex gap-3">
                      <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-100 text-sm font-bold text-teal-700 dark:bg-teal-900 dark:text-teal-300">
                        {step.step}
                      </div>
                      <div className="flex-1 space-y-2">
                        <div className="flex items-center gap-2">
                          <h4 className="text-sm font-semibold text-slate-900 dark:text-white" dir="rtl">
                            {step.title_ar}
                          </h4>
                          <span className="text-xs text-slate-400">{step.title_en}</span>
                        </div>
                        <p className="text-xs text-slate-500" dir="rtl">{step.details_ar}</p>
                        <CodeBlock code={step.cmd} />
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Demo Credentials */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Key className="h-5 w-5 text-teal-500" />
                  بيانات الدخول التجريبية | Demo Credentials
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                  {demoCredentials.map((cred) => (
                    <div key={cred.role} className="rounded-lg border p-3">
                      <div className="flex items-center gap-2 mb-2">
                        {projectInfo.roles.find((r) => r.name === cred.role)?.icon && (
                          <span className="text-lg">
                            {projectInfo.roles.find((r) => r.name === cred.role)!.icon}
                          </span>
                        )}
                        <span className="text-sm font-semibold text-slate-900 dark:text-white">
                          {cred.name_ar}
                        </span>
                      </div>
                      <div className="space-y-1 text-xs">
                        <div className="flex items-center justify-between">
                          <span className="text-slate-500">Email:</span>
                          <code className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] dark:bg-slate-800">
                            {cred.email}
                          </code>
                        </div>
                        <div className="flex items-center justify-between">
                          <span className="text-slate-500">Password:</span>
                          <code className="rounded bg-slate-100 px-1.5 py-0.5 font-mono text-[11px] dark:bg-slate-800">
                            {cred.password}
                          </code>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Important URLs */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <ExternalLink className="h-5 w-5 text-teal-500" />
                  الروابط المهمة | Important URLs
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-2">
                  {[
                    { label: 'API Base URL', url: '/api/v1', desc: 'جميع نقاط النهاية' },
                    { label: 'Swagger Documentation', url: '/api/documentation', desc: 'توثيق API التفاعلي' },
                    { label: 'Health Check', url: '/api/v1/languages', desc: 'فحص حالة الخادم' },
                    { label: 'phpMyAdmin', url: 'http://localhost:8080', desc: 'إدارة قاعدة البيانات (Docker)' },
                  ].map((link) => (
                    <div
                      key={link.label}
                      className="flex items-center justify-between rounded-lg border p-3 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50"
                    >
                      <div>
                        <p className="text-sm font-semibold text-slate-900 dark:text-white">{link.label}</p>
                        <p className="text-xs text-slate-500">{link.desc}</p>
                      </div>
                      <code className="rounded bg-teal-50 px-2 py-1 text-xs font-mono text-teal-700 dark:bg-teal-900/50 dark:text-teal-300" dir="ltr">
                        {link.url}
                      </code>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* API TESTER TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="tester" className="space-y-6">
            <Card className="border-teal-200 bg-gradient-to-r from-teal-50/50 to-emerald-50/50 dark:border-teal-800">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <TestTube className="h-5 w-5 text-teal-500" />
                  اختبار API | API Tester
                </CardTitle>
                <CardDescription>
                  استخدم هذه الأداة لاختبار الـ API. أدخل الـ Base URL الخاص بك واختبر النقاط مباشرة.
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <CodeBlock
                  code={`# ═══════════════════════════════════════════════════
# 1. تسجيل الدخول - Login
# ═══════════════════════════════════════════════════════════════
curl -X POST ${baseUrl}/api/v1/auth/login \\
  -H "Content-Type: application/json" \\
  -H "Accept-Language: ar" \\
  -d '{"email":"patient@example.com","password":"password"}'

# ═══════════════════════════════════════════════════════════════
# 2. عرض الملف الشخصي - Get Profile
# ═══════════════════════════════════════════════════════════════
curl -X GET ${baseUrl}/api/v1/patient/profile \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept-Language: ar"

# ═══════════════════════════════════════════════════════════════
# 3. حجز موعد - Book Appointment
# ═══════════════════════════════════════════════════════════════
curl -X POST ${baseUrl}/api/v1/patient/appointments \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "doctor_id": 1,
    "department_id": 1,
    "appointment_date": "2026-08-20",
    "appointment_time": "10:00",
    "type": "consultation",
    "payment_method": "cash"
  }'

# ═══════════════════════════════════════════════════════════════
# 4. حالة الطابور - Queue Status
# ═══════════════════════════════════════════════════════════════
curl -X GET ${baseUrl}/api/v1/patient/queue-status \\
  -H "Authorization: Bearer YOUR_TOKEN" \\
  -H "Accept-Language: ar"

# ═══════════════════════════════════════════════════════════════
# 5. لوحة تحكم الطبيب - Doctor Dashboard
# ═══════════════════════════════════════════════════════════════
curl -X GET ${baseUrl}/api/v1/doctor/dashboard \\
  -H "Authorization: Bearer DOCTOR_TOKEN" \\
  -H "Accept-Language: en"

# ═══════════════════════════════════════════════════════════════
# 6. استدعاء المريض التالي (استقبال) - Call Next
# ═══════════════════════════════════════════════════════════════
curl -X POST ${baseUrl}/api/v1/receptionist/queue/1/call \\
  -H "Authorization: Bearer RECEPTIONIST_TOKEN" \\
  -H "Accept-Language: ar"

# ═══════════════════════════════════════════════════════════════
# 7. صرف وصفة (صيدلي) - Dispense Prescription
# ═══════════════════════════════════════════════════════════════
curl -X PUT ${baseUrl}/api/v1/pharmacist/prescriptions/1/dispense \\
  -H "Authorization: Bearer PHARMACIST_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{"dispensed_items":[{"prescription_item_id":1,"quantity_dispensed":30}]}'

# ═══════════════════════════════════════════════════════════════
# 8. إنشاء سجل طبي (طبيب) - Create Medical Record
# ═══════════════════════════════════════════════════════════════
curl -X POST ${baseUrl}/api/v1/doctor/medical-records \\
  -H "Authorization: Bearer DOCTOR_TOKEN" \\
  -H "Content-Type: application/json" \\
  -d '{
    "patient_id": 1,
    "appointment_id": 1,
    "diagnosis": "الأنفلونزا الموسمية",
    "symptoms": "حمى، سعال، آلام الجسم",
    "notes": "يحتاج راحة لمدة 3 أيام",
    "vital_signs": {
      "temperature": 38.5,
      "blood_pressure_systolic": 120,
      "blood_pressure_diastolic": 80,
      "heart_rate": 72
    }
  }'

# ═══════════════════════════════════════════════════════════════
# 9. المستشفيات العامة (بدون مصادقة)
# ═══════════════════════════════════════════════════════════════
curl -X GET ${baseUrl}/api/v1/hospitals \\
  -H "Accept-Language: ar"

curl -X GET ${baseUrl}/api/v1/hospitals/1/doctors \\
  -H "Accept-Language: ar"

# ═══════════════════════════════════════════════════════════════
# 10. لوحة تحكم المدير - Admin Dashboard
# ═══════════════════════════════════════════════════════════════
curl -X GET ${baseUrl}/api/v1/admin/dashboard \\
  -H "Authorization: Bearer ADMIN_TOKEN" \\
  -H "Accept-Language: ar"`}
                />
              </CardContent>
            </Card>

            {/* Postman Collection Hint */}
            <Card>
              <CardContent className="flex items-center gap-4 p-4">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-orange-600">
                  <ExternalLink className="h-5 w-5" />
                </div>
                <div className="flex-1">
                  <p className="text-sm font-semibold text-slate-900 dark:text-white">
                    Swagger UI Documentation
                  </p>
                  <p className="text-xs text-slate-500">
                    بعد تشغيل المشروع، افتح Swagger UI لاختبار جميع الـ API مباشرة من المتصفح
                  </p>
                </div>
                <code className="hidden rounded bg-teal-50 px-2 py-1 text-xs font-mono text-teal-700 dark:bg-teal-900/50 dark:text-teal-300 sm:block" dir="ltr">
                  {baseUrl}/api/documentation
                </code>
              </CardContent>
            </Card>
          </TabsContent>

          {/* ═══════════════════════════════════════════════════════════ */}
          {/* MOBILE APP TAB */}
          {/* ═══════════════════════════════════════════════════════════ */}
          <TabsContent value="mobile" className="space-y-6">
            <Card className="border-teal-200 bg-gradient-to-r from-teal-50/50 to-emerald-50/50 dark:border-teal-800">
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Smartphone className="h-5 w-5 text-teal-500" />
                  تطبيق الموبايل | Mobile App (Flutter)
                </CardTitle>
                <CardDescription>
                  تطبيق Flutter مستقل متصل بنفس الـ API - يدعم Android و iOS
                </CardDescription>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                  {[
                    { icon: Smartphone, title: 'Clean Architecture', desc: 'فصل كامل بين Presentation و Domain و Data layers', titleAr: 'هندسة نظيفة' },
                    { icon: Activity, title: 'BLoC Pattern', desc: 'إدارة حالة التطبيق بـ Business Logic Component', titleAr: 'نمط BLoC' },
                    { icon: Database, title: 'Hive Cache', desc: 'تخزين محلي سريع للبيانات المكررة', titleAr: 'ذاكرة Hive' },
                    { icon: Layers, title: 'Dio HTTP', desc: 'عميل HTTP متقدم مع Interceptors', titleAr: 'Dio HTTP' },
                    { icon: Key, title: 'GetIt DI', desc: 'حقن التبعيات لحلول قابلة للاختبار', titleAr: 'حقن التبعيات' },
                    { icon: Globe, title: 'RTL/LTR', desc: 'دعم كامل للعربي والإنجليزي', titleAr: 'RTL/LTR' },
                  ].map((f) => (
                    <div key={f.title} className="flex items-start gap-3 rounded-lg border p-3">
                      <f.icon className="mt-0.5 h-4 w-4 shrink-0 text-teal-500" />
                      <div>
                        <p className="text-sm font-semibold text-slate-900 dark:text-white">{f.title}</p>
                        <p className="text-xs text-slate-500">{f.desc}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Flutter Screens */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Smartphone className="h-5 w-5 text-teal-500" />
                  شاشات التطبيق | App Screens
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                  {[
                    { name: 'Splash Screen', nameAr: 'شاشة البداية', role: 'عام' },
                    { name: 'Login/Register', nameAr: 'تسجيل الدخول', role: 'عام' },
                    { name: 'Home/Dashboard', nameAr: 'الرئيسية', role: 'جميع الأدوار' },
                    { name: 'Patient Profile', nameAr: 'الملف الشخصي', role: 'مريض' },
                    { name: 'Book Appointment', nameAr: 'حجز موعد', role: 'مريض' },
                    { name: 'Queue Status', nameAr: 'حالة الطابور', role: 'مريض' },
                    { name: 'Medical Records', nameAr: 'السجلات الطبية', role: 'مريض + طبيب' },
                    { name: 'Prescriptions', nameAr: 'الوصفات', role: 'مريض + طبيب + صيدلي' },
                    { name: 'Invoices & Payments', nameAr: 'الفواتير والدفع', role: 'مريض' },
                    { name: 'Notifications', nameAr: 'الإشعارات', role: 'جميع الأدوار' },
                    { name: 'Doctor Schedule', nameAr: 'جدول الطبيب', role: 'طبيب' },
                    { name: 'Patient Queue', nameAr: 'طابور المرضى', role: 'استقبال' },
                    { name: 'Medication Inventory', nameAr: 'مخزون الأدوية', role: 'صيدلي' },
                    { name: 'Admin Dashboard', nameAr: 'لوحة المدير', role: 'مدير' },
                    { name: 'Settings', nameAr: 'الإعدادات', role: 'جميع الأدوار' },
                  ].map((screen) => (
                    <div key={screen.name} className="flex items-center gap-2 rounded-lg border p-2.5 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50">
                      <ChevronRight className="h-4 w-4 text-teal-500" />
                      <div className="flex-1">
                        <p className="text-xs font-semibold text-slate-900 dark:text-white">{screen.name}</p>
                        <p className="text-[10px] text-slate-500" dir="rtl">{screen.nameAr}</p>
                      </div>
                      <Badge variant="outline" className="text-[10px]">
                        {screen.role}
                      </Badge>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>

            {/* Download */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Download className="h-5 w-5 text-teal-500" />
                  تحميل المشروع | Download Project
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-3">
                <p className="text-sm text-slate-600 dark:text-slate-400" dir="rtl">
                  ملف ZIP جاهز للتحميل والرفع على GitHub. يحتوي على كود Laravel الكامل مع جميع الملفات.
                </p>
                <div className="flex flex-wrap gap-3">
                  <Button className="gap-2 bg-gradient-to-r from-teal-500 to-emerald-600 text-white hover:from-teal-600 hover:to-emerald-700">
                    <Download className="h-4 w-4" />
                    medicare-pro.zip (347 KB)
                  </Button>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </main>

      {/* Footer */}
      <footer className="mt-12 border-t border-slate-200 dark:border-slate-800">
        <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
          <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <div className="flex items-center gap-2 text-sm text-slate-500">
              <span className="text-lg">🏥</span>
              <span>MediCare Pro &copy; 2026</span>
              <span className="mx-1">|</span>
              <span>MIT License</span>
            </div>
            <div className="flex items-center gap-4 text-xs text-slate-400">
              <span>Laravel 11 + PHP 8.3</span>
              <span>•</span>
              <span>MySQL 8 + Redis</span>
              <span>•</span>
              <span>80+ API Endpoints</span>
              <span>•</span>
              <span>Flutter Mobile</span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

/* ═══════════════════════════════════════════════════════════════════ */
/* Sub-components */
/* ═══════════════════════════════════════════════════════════════════ */

function EndpointRow({
  endpoint,
  baseUrl,
  globalIdx,
  copiedIdx,
  onCopy,
}: {
  endpoint: ApiEndpoint & { category: string }
  baseUrl: string
  globalIdx: number
  copiedIdx: number | null
  onCopy: (text: string, idx: number) => void
}) {
  const fullUrl = `${baseUrl}${endpoint.path}`
  const uid = `${endpoint.method}-${endpoint.path}-${endpoint.category}`.replace(/[^a-zA-Z0-9]/g, '_')

  return (
    <div className="group flex items-start gap-2 rounded-lg border p-2.5 transition-all hover:bg-slate-50 dark:hover:bg-slate-800/30">
      <Badge className={`shrink-0 text-[10px] font-bold px-1.5 py-0 ${methodColors[endpoint.method] || ''}`}>
        {endpoint.method}
      </Badge>

      <div className="min-w-0 flex-1">
        <div className="flex items-center gap-1.5">
          <code className="truncate text-xs font-mono font-semibold text-slate-900 dark:text-white" dir="ltr">
            {endpoint.path}
          </code>
          <button
            onClick={() => onCopy(fullUrl, globalIdx)}
            className="shrink-0 opacity-0 transition-opacity group-hover:opacity-100"
          >
            {copiedIdx === globalIdx ? (
              <CheckCircle2 className="h-3 w-3 text-green-500" />
            ) : (
              <Copy className="h-3 w-3 text-slate-400" />
            )}
          </button>
        </div>
        <p className="mt-0.5 text-[11px] text-slate-500" dir="rtl">{endpoint.description_ar}</p>

        {/* Params */}
        {endpoint.params && endpoint.params.length > 0 && (
          <div className="mt-1.5 flex flex-wrap gap-1">
            {endpoint.params.map((p) => (
              <Badge key={p.name} variant="outline" className="text-[9px] px-1 py-0" dir="ltr">
                {p.name}: {p.type}
              </Badge>
            ))}
          </div>
        )}

        {/* Body */}
        {endpoint.body && endpoint.body.length > 0 && (
          <div className="mt-1.5 flex flex-wrap gap-1">
            {endpoint.body.filter((b) => b.required).map((b) => (
              <Badge key={b.name} className="text-[9px] px-1 py-0 bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400" dir="ltr">
                {b.name}
              </Badge>
            ))}
          </div>
        )}

        {/* Roles */}
        <div className="mt-1 flex items-center gap-1">
          {endpoint.auth ? (
            <Badge variant="secondary" className="text-[9px] gap-0.5">
              <Shield className="h-2.5 w-2.5" />
              {endpoint.roles.join(', ')}
            </Badge>
          ) : (
            <Badge variant="secondary" className="text-[9px]">
              🌐 Public
            </Badge>
          )}
        </div>
      </div>
    </div>
  )
}

function CodeBlock({ code }: { code: string }) {
  const [copied, setCopied] = useState(false)

  return (
    <div className="group relative rounded-lg border bg-slate-950">
      <button
        onClick={() => {
          navigator.clipboard.writeText(code.trim())
          setCopied(true)
          setTimeout(() => setCopied(false), 2000)
        }}
        className="absolute top-2 right-2 rounded bg-slate-800 p-1.5 text-slate-400 transition-colors hover:bg-slate-700 hover:text-white"
      >
        {copied ? <CheckCircle2 className="h-3 w-3 text-green-400" /> : <Copy className="h-3 w-3" />}
      </button>
      <ScrollArea className="max-h-96">
        <pre className="overflow-x-auto p-3 text-xs leading-relaxed text-slate-300" dir="ltr">
          {code}
        </pre>
      </ScrollArea>
    </div>
  )
}
