'use client'

import { useState } from 'react'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Separator } from '@/components/ui/separator'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  Hospital,
  Search,
  Star,
  Clock,
  MapPin,
  Phone,
  Mail,
  Calendar,
  ChevronLeft,
  ChevronRight,
  ExternalLink,
  Shield,
  Heart,
  Users,
  Stethoscope,
  TicketCheck,
  FileText,
  CreditCard,
  Bell,
  User,
  Menu,
  X,
  ArrowLeft,
  CheckCircle2,
  Globe,
  Zap,
  Smartphone,
  Baby,
  Eye,
  Bone,
  Pill,
  Microscope,
} from 'lucide-react'

// ═══════════════════════════════════════════════════════════
// DATA
// ═══════════════════════════════════════════════════════════

const hospitals = [
  { id: 1, name_ar: 'مستشفى ميدي كير المركزي', name_en: 'MediCare Central Hospital', location: 'الرياض، حي العليا', doctors: 42, departments: 8, rating: 4.8, image: '🏥' },
  { id: 2, name_ar: 'مستشفى ميدي كير الشمال', name_en: 'MediCare North Hospital', location: 'الرياض، حي الملقا', doctors: 28, departments: 6, rating: 4.6, image: '🏥' },
  { id: 3, name_ar: 'عيادة ميدي كير - جدة', name_en: 'MediCare Jeddah Clinic', location: 'جدة، حي الروضة', doctors: 15, departments: 5, rating: 4.7, image: '🏢' },
]

const departments = [
  { id: 1, name_ar: 'القلب والأوعية الدموية', name_en: 'Cardiology', icon: Heart, color: 'text-red-500', doctors: 6, desc_ar: 'تشخيص وعلاج أمراض القلب والشرايين' },
  { id: 2, name_ar: 'الباطنة', name_en: 'Internal Medicine', icon: Stethoscope, color: 'text-blue-500', doctors: 5, desc_ar: 'الطب الباطني العام وأمراض الجهاز الهضمي' },
  { id: 3, name_ar: 'طب الأطفال', name_en: 'Pediatrics', icon: Baby, color: 'text-pink-500', doctors: 4, desc_ar: 'رعاية صحية شاملة للأطفال والرضع' },
  { id: 4, name_ar: 'جراحة العظام', name_en: 'Orthopedics', icon: Bone, color: 'text-amber-500', doctors: 3, desc_ar: 'علاج إصابات و أمراض العظام والمفاصل' },
  { id: 5, name_ar: 'الأمراض الجلدية', name_en: 'Dermatology', icon: Eye, color: 'text-purple-500', doctors: 3, desc_ar: 'علاج الأمراض الجلدية والتجميل' },
  { id: 6, name_ar: 'طب العيون', name_en: 'Ophthalmology', icon: Eye, color: 'text-cyan-500', doctors: 4, desc_ar: 'فحص وعلاج أمراض العيون والرؤية' },
  { id: 7, name_ar: 'الأنف والأذن والحنجرة', name_en: 'ENT', icon: Stethoscope, color: 'text-orange-500', doctors: 3, desc_ar: 'أمراض الأنف والأذن والحنجرة' },
  { id: 8, name_ar: 'الأشعة والتشخيص', name_en: 'Radiology', icon: Microscope, color: 'text-teal-500', doctors: 2, desc_ar: 'خدمات الأشعة والتصوير الطبي' },
]

const doctors = [
  { id: 1, name_ar: 'د. سارة علي', name_en: 'Dr. Sarah Ali', specialty_ar: 'أمراض البطانة', specialty_en: 'Internal Medicine', dept: 'الباطنة', rating: 4.8, reviews: 156, patients: 342, fee: 200, available: true, image: '👩‍⚕️', schedule: 'السبت - الأربعاء | 9ص - 5م' },
  { id: 2, name_ar: 'د. خالد يوسف', name_en: 'Dr. Khaled Youssef', specialty_ar: 'جراحة القلب', specialty_en: 'Cardiac Surgery', dept: 'القلب', rating: 4.9, reviews: 203, patients: 289, fee: 350, available: true, image: '👨‍⚕️', schedule: 'الأحد - الخميس | 8ص - 4م' },
  { id: 3, name_ar: 'د. منى عبدالله', name_en: 'Dr. Mona Abdullah', specialty_ar: 'طب الأطفال', specialty_en: 'Pediatrics', dept: 'الأطفال', rating: 4.7, reviews: 98, patients: 198, fee: 150, available: true, image: '👩‍⚕️', schedule: 'السبت - الخميس | 10ص - 6م' },
  { id: 4, name_ar: 'د. محمد رضا', name_en: 'Dr. Mohamed Reda', specialty_ar: 'جراحة العظام', specialty_en: 'Orthopedic Surgery', dept: 'العظام', rating: 4.5, reviews: 67, patients: 156, fee: 280, available: false, image: '👨‍⚕️', schedule: 'الإثنين - الجمعة | 9ص - 3م' },
  { id: 5, name_ar: 'د. هالة سامي', name_en: 'Dr. Hala Sami', specialty_ar: 'أمراض جلدية', specialty_en: 'Dermatology', dept: 'الجلدية', rating: 4.6, reviews: 112, patients: 267, fee: 180, available: true, image: '👩‍⚕️', schedule: 'السبت - الأربعاء | 11ص - 7م' },
  { id: 6, name_ar: 'د. عمرو حسين', name_en: 'Dr. Amr Hussein', specialty_ar: 'طب العيون', specialty_en: 'Ophthalmology', dept: 'العيون', rating: 4.4, reviews: 89, patients: 312, fee: 220, available: true, image: '👨‍⚕️', schedule: 'الأحد - الخميس | 10ص - 5م' },
]

const reviews = [
  { id: 1, patient: 'أحمد محمد', rating: 5, comment: 'دكتور ممتاز واستقبال حلو والتنظيم رائع', date: '2026-08-10' },
  { id: 2, patient: 'فاطمة حسن', rating: 4, comment: 'خدمة جيدة لكن وقت الانتظار طويل', date: '2026-08-08' },
  { id: 3, patient: 'عمر سعيد', rating: 5, comment: 'أفضل مستشفى في المنطقة، أنصح الكل', date: '2026-08-05' },
]

const timeSlots = ['08:00', '08:30', '09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '13:00', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30', '17:00', '17:30']

// ═══════════════════════════════════════════════════════════
// MAIN COMPONENT
// ═══════════════════════════════════════════════════════════

type Page = 'home' | 'hospital' | 'doctors' | 'doctor-profile' | 'departments' | 'booking' | 'login' | 'register' | 'profile' | 'queue' | 'records' | 'prescriptions' | 'invoices' | 'notifications'

export default function Home() {
  const [currentPage, setCurrentPage] = useState<Page>('home')
  const [searchQuery, setSearchQuery] = useState('')
  const [selectedHospital, setSelectedHospital] = useState<number>(1)
  const [selectedDoctor, setSelectedDoctor] = useState<number>(0)
  const [selectedDate, setSelectedDate] = useState('')
  const [selectedTime, setSelectedTime] = useState('')
  const [selectedDept, setSelectedDept] = useState<number>(0)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [bookingSuccess, setBookingSuccess] = useState(false)

  const navigate = (page: Page, extra?: any) => {
    setCurrentPage(page)
    if (extra?.doctorId) setSelectedDoctor(extra.doctorId)
    if (extra?.deptId) setSelectedDept(extra.deptId)
    if (extra?.hospitalId) setSelectedHospital(extra.hospitalId)
    setBookingSuccess(false)
    window.scrollTo(0, 0)
  }

  const handleBooking = () => {
    setBookingSuccess(true)
  }

  return (
    <div className="min-h-screen bg-gray-50" dir="rtl">
      {/* ═══════════════════════════════════════════════════════ */}
      {/* NAVIGATION BAR */}
      {/* ═══════════════════════════════════════════════════════ */}
      <nav className="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b shadow-sm">
        <div className="mx-auto max-w-6xl px-4 flex items-center justify-between h-14">
          <button onClick={() => navigate('home')} className="flex items-center gap-2">
            <span className="text-xl">🏥</span>
            <span className="font-bold text-teal-700 text-sm sm:text-base">MediCare Pro</span>
          </button>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center gap-1">
            <NavButton onClick={() => navigate('home')} active={currentPage === 'home'}>الرئيسية</NavButton>
            <NavButton onClick={() => navigate('doctors')}>الأطباء</NavButton>
            <NavButton onClick={() => navigate('departments')}>الأقسام</NavButton>
            <NavButton onClick={() => navigate('login')}>تسجيل الدخول</NavButton>
          </div>

          <div className="flex items-center gap-2">
            <Button
              size="sm"
              className="bg-gradient-to-r from-teal-500 to-emerald-600 text-white hover:from-teal-600 hover:to-emerald-700 hidden sm:flex"
              onClick={() => navigate('register')}
            >
              <User className="h-3.5 w-3.5 ml-1" />
              حساب جديد
            </Button>
            <Button
              variant="outline"
              size="sm"
              className="hidden sm:flex gap-1"
              onClick={() => navigate('login')}
            >
              تسجيل الدخول
            </Button>
            <Button
              size="sm"
              variant="ghost"
              className="md:hidden"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </Button>
          </div>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <div className="md:hidden border-t bg-white">
            <div className="px-4 py-3 space-y-1">
              <MobileNavButton onClick={() => { navigate('home'); setMobileMenuOpen(false) }}>🏠 الرئيسية</MobileNavButton>
              <MobileNavButton onClick={() => { navigate('doctors'); setMobileMenuOpen(false) }}>👨‍⚕️ الأطباء</MobileNavButton>
              <MobileNavButton onClick={() => { navigate('departments'); setMobileMenuOpen(false) }}>🏢 الأقسام</MobileNavButton>
              <MobileNavButton onClick={() => { navigate('login'); setMobileMenuOpen(false) }}>🔑 تسجيل الدخول</MobileNavButton>
              <Separator />
              <Button className="w-full bg-gradient-to-r from-teal-500 to-emerald-600 text-white" onClick={() => { navigate('register'); setMobileMenuOpen(false) }}>
                إنشاء حساب جديد
              </Button>
            </div>
          </div>
        )}
      </nav>

      {/* ═══════════════════════════════════════════════════════ */}
      {/* MAIN CONTENT */}
      {/* ═══════════════════════════════════════════════════════ */}
      <main className="mx-auto max-w-6xl px-4 py-6">
        {/* HOME PAGE */}
        {currentPage === 'home' && (
          <div className="space-y-8">
            {/* Hero */}
            <section className="relative overflow-hidden rounded-2xl bg-gradient-to-l from-teal-600 to-emerald-700 text-white p-8 sm:p-12">
              <div className="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2" />
              <div className="absolute bottom-0 right-0 w-48 h-48 bg-white/5 rounded-full translate-x-1/4 translate-y-1/4" />
              <div className="relative z-10 max-w-xl">
                <h1 className="text-3xl sm:text-4xl font-bold mb-3">رعاية صحية أفضل لعائلتك</h1>
                <p className="text-teal-100 mb-6 text-sm sm:text-base">
                  احجز موعدك بسهولة مع أفضل الأطباء. نظام طوابير ذكي، إشعارات فورية، ودفع إلكتروني آمن.
                </p>
                <div className="flex flex-wrap gap-3">
                  <Button size="lg" className="bg-white text-teal-700 hover:bg-teal-50 font-semibold" onClick={() => navigate('doctors')}>
                    <Search className="h-4 w-4 ml-2" />
                    احجز موعدك الآن
                  </Button>
                  <Button size="lg" variant="outline" className="border-white/30 text-white hover:bg-white/10" onClick={() => navigate('departments')}>
                    تصفح الأقسام
                  </Button>
                </div>
              </div>
            </section>

            {/* Quick Search */}
            <section>
              <div className="relative max-w-2xl mx-auto">
                <Search className="absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
                <Input
                  placeholder="ابحث عن طبيب، قسم، أو مستشفى..."
                  className="pr-10 h-12 text-sm rounded-xl border-gray-200"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                />
              </div>
            </section>

            {/* Stats */}
            <section className="grid grid-cols-2 sm:grid-cols-4 gap-3">
              {[
                { icon: Hospital, label: 'مستشفى', value: '3', color: 'from-teal-500 to-emerald-600' },
                { icon: Users, label: 'طبيب', value: '42', color: 'from-blue-500 to-indigo-600' },
                { icon: Star, label: 'تقييم', value: '4.8', color: 'from-amber-500 to-orange-600' },
                { icon: Clock, label: 'متوسط الانتظار', value: '18 د', color: 'from-rose-500 to-pink-600' },
              ].map((stat) => (
                <div key={stat.label} className="bg-white rounded-xl p-4 text-center shadow-sm border">
                  <div className={`inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br ${stat.color} text-white mb-2`}>
                    <stat.icon className="h-5 w-5" />
                  </div>
                  <p className="text-xl font-bold">{stat.value}</p>
                  <p className="text-xs text-gray-500">{stat.label}</p>
                </div>
              ))}
            </section>

            {/* Hospitals */}
            <section>
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-bold text-gray-900">المستشفيات والعيادات</h2>
                <Button variant="ghost" size="sm">عرض الكل ←</Button>
              </div>
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {hospitals.map((h) => (
                  <Card key={h.id} className="overflow-hidden hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('hospital', { hospitalId: h.id })}>
                    <div className="bg-gradient-to-l from-teal-500/10 to-emerald-500/10 p-6 text-center">
                      <span className="text-5xl">{h.image}</span>
                    </div>
                    <CardContent className="p-4">
                      <h3 className="font-bold text-sm">{h.name_ar}</h3>
                      <p className="text-xs text-gray-500 flex items-center gap-1 mt-1">
                        <MapPin className="h-3 w-3" /> {h.location}
                      </p>
                      <div className="flex justify-between items-center mt-3 pt-3 border-t text-xs">
                        <span className="text-gray-500">{h.doctors} طبيب</span>
                        <span className="flex items-center gap-0.5 text-amber-500 font-semibold">
                          <Star className="h-3 w-3 fill-current" /> {h.rating}
                        </span>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </section>

            {/* Departments Quick Links */}
            <section>
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-bold text-gray-900">الأقسام الطبية</h2>
                <Button variant="ghost" size="sm" onClick={() => navigate('departments')}>عرض الكل ←</Button>
              </div>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                {departments.slice(0, 4).map((dept) => (
                  <button
                    key={dept.id}
                    className="bg-white rounded-xl p-4 text-center shadow-sm border hover:shadow-md hover:border-teal-200 transition-all"
                    onClick={() => navigate('doctors')}
                  >
                    <dept.icon className={`h-8 w-8 mx-auto mb-2 ${dept.color}`} />
                    <p className="text-sm font-semibold">{dept.name_ar}</p>
                    <p className="text-[10px] text-gray-400">{dept.doctors} أطباء</p>
                  </button>
                ))}
              </div>
            </section>

            {/* Top Doctors */}
            <section>
              <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-bold text-gray-900">أفضل الأطباء</h2>
                <Button variant="ghost" size="sm" onClick={() => navigate('doctors')}>عرض الكل ←</Button>
              </div>
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {doctors.slice(0, 3).map((doc) => (
                  <Card key={doc.id} className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('doctor-profile', { doctorId: doc.id })}>
                    <CardContent className="p-4">
                      <div className="flex items-start gap-3">
                        <div className="w-14 h-14 rounded-xl bg-teal-50 flex items-center justify-center text-2xl shrink-0">
                          {doc.image}
                        </div>
                        <div className="flex-1 min-w-0">
                          <h3 className="font-bold text-sm">{doc.name_ar}</h3>
                          <p className="text-xs text-gray-500">{doc.specialty_ar} - {doc.dept}</p>
                          <div className="flex items-center gap-2 mt-1.5">
                            <span className="flex items-center gap-0.5 text-xs text-amber-500 font-semibold">
                              <Star className="h-3 w-3 fill-current" /> {doc.rating}
                            </span>
                            <Badge variant="outline" className="text-[10px]">
                              {doc.fee} ر.س
                            </Badge>
                            {doc.available && (
                              <Badge className="text-[10px] bg-green-50 text-green-600">متاح</Badge>
                            )}
                          </div>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </section>

            {/* Features */}
            <section>
              <h2 className="text-lg font-bold text-gray-900 mb-4 text-center">لماذا MediCare Pro؟</h2>
              <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
                {[
                  { icon: Clock, title: 'نظام طوابير ذكي', desc: 'رقم طابور تلقائي مع تقدير وقت الانتظار', color: 'text-blue-500' },
                  { icon: Bell, title: 'إشعارات فورية', desc: 'تنبيهات عبر Push و SMS عند دورك', color: 'text-amber-500' },
                  { icon: CreditCard, title: 'دفع إلكتروني', desc: 'ادفع بسهولة بالبطاقة أو المحفظة أو التأمين', color: 'text-green-500' },
                  { icon: Shield, title: 'أمن وخصوصية', desc: 'حماية كاملة لبياناتك الطبية', color: 'text-purple-500' },
                ].map((f) => (
                  <div key={f.title} className="bg-white rounded-xl p-4 border shadow-sm text-center">
                    <f.icon className={`h-8 w-8 mx-auto mb-2 ${f.color}`} />
                    <p className="text-sm font-semibold">{f.title}</p>
                    <p className="text-[10px] text-gray-500 mt-1">{f.desc}</p>
                  </div>
                ))}
              </div>
            </section>

            {/* Reviews */}
            <section>
              <h2 className="text-lg font-bold text-gray-900 mb-4">آراء المرضى</h2>
              <div className="grid sm:grid-cols-3 gap-3">
                {reviews.map((r) => (
                  <Card key={r.id}>
                    <CardContent className="p-4">
                      <div className="flex items-center gap-1 mb-2">
                        {Array.from({ length: r.rating }).map((_, i) => (
                          <Star key={i} className="h-3 w-3 fill-amber-400 text-amber-400" />
                        ))}
                      </div>
                      <p className="text-sm text-gray-700">{r.comment}</p>
                      <p className="text-xs text-gray-400 mt-2">{r.patient} • {r.date}</p>
                    </CardContent>
                  </Card>
                ))}
              </div>
            </section>

            {/* Link to Admin */}
            <section className="bg-gradient-to-l from-slate-700 to-slate-800 rounded-2xl p-6 text-white text-center">
              <h3 className="font-bold text-lg mb-2">لوحة إدارة المستشفى</h3>
              <p className="text-sm text-slate-300 mb-4">للموظفين والأطباء - الدخول عبر لوحة الإدارة المنفصلة</p>
              <Button className="bg-white text-slate-700 hover:bg-slate-100 gap-1" onClick={() => navigate('login')}>
                <ExternalLink className="h-3.5 w-3.5" />
                الدخول كموظف
              </Button>
            </section>
          </div>
        )}

        {/* DOCTORS LIST PAGE */}
        {currentPage === 'doctors' && (
          <div className="space-y-6">
            <div className="flex items-center gap-3 mb-2">
              <Button variant="ghost" size="sm" onClick={() => navigate('home')}>
                <ArrowLeft className="h-4 w-4" />
              </Button>
              <h2 className="text-xl font-bold">الأطباء</h2>
            </div>

            {/* Filters */}
            <div className="flex flex-wrap gap-2">
              <Button size="sm" variant={selectedDept === 0 ? 'default' : 'outline'} onClick={() => setSelectedDept(0)}>الكل</Button>
              {departments.map((d) => (
                <Button key={d.id} size="sm" variant={selectedDept === d.id ? 'default' : 'outline'} onClick={() => setSelectedDept(d.id)}>
                  {d.name_ar}
                </Button>
              ))}
            </div>

            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {doctors
                .filter((d) => selectedDept === 0 || departments[selectedDept - 1]?.name_ar === d.dept)
                .filter((d) => !searchQuery || d.name_ar.includes(searchQuery) || d.specialty_ar.includes(searchQuery))
                .map((doc) => (
                  <Card key={doc.id} className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('doctor-profile', { doctorId: doc.id })}>
                    <CardContent className="p-5">
                      <div className="flex items-start gap-3 mb-3">
                        <div className="w-14 h-14 rounded-xl bg-teal-50 flex items-center justify-center text-2xl shrink-0">
                          {doc.image}
                        </div>
                        <div className="flex-1">
                          <h3 className="font-bold">{doc.name_ar}</h3>
                          <p className="text-xs text-gray-500">{doc.specialty_ar}</p>
                          <p className="text-[10px] text-gray-400">{doc.dept}</p>
                        </div>
                      </div>
                      <div className="flex flex-wrap items-center gap-2 mb-3">
                        <span className="flex items-center gap-0.5 text-sm text-amber-500 font-semibold">
                          <Star className="h-3.5 w-3.5 fill-current" /> {doc.rating}
                        </span>
                        <Badge variant="outline" className="text-[10px]">{doc.reviews} تقييم</Badge>
                        <Badge variant="outline" className="text-[10px]">{doc.patients} مريض</Badge>
                        {doc.available ? (
                          <Badge className="text-[10px] bg-green-50 text-green-600">متاح</Badge>
                        ) : (
                          <Badge className="text-[10px] bg-gray-100 text-gray-500">غير متاح</Badge>
                        )}
                      </div>
                      <div className="flex items-center justify-between pt-3 border-t">
                        <span className="text-xs text-gray-500">📅 {doc.schedule}</span>
                        <span className="text-sm font-bold text-teal-600">{doc.fee} ر.س</span>
                      </div>
                    </CardContent>
                  </Card>
                ))}
            </div>
          </div>
        )}

        {/* DOCTOR PROFILE PAGE */}
        {currentPage === 'doctor-profile' && selectedDoctor > 0 && (() => {
          const doc = doctors.find((d) => d.id === selectedDoctor) || doctors[0]
          return (
            <div className="space-y-6">
              <Button variant="ghost" size="sm" onClick={() => navigate('doctors')} className="mb-2">
                <ArrowLeft className="h-4 w-4 ml-1" /> العودة للأطباء
              </Button>

              {/* Doctor Header */}
              <Card>
                <CardContent className="p-6">
                  <div className="flex flex-col sm:flex-row items-start gap-4">
                    <div className="w-20 h-20 rounded-2xl bg-teal-50 flex items-center justify-center text-4xl shrink-0">
                      {doc.image}
                    </div>
                    <div className="flex-1">
                      <div className="flex flex-wrap items-center gap-2 mb-1">
                        <h2 className="text-xl font-bold">{doc.name_ar}</h2>
                        {doc.available ? (
                          <Badge className="bg-green-50 text-green-600">متاح للحجز</Badge>
                        ) : (
                          <Badge className="bg-gray-100 text-gray-500">غير متاح حالياً</Badge>
                        )}
                      </div>
                      <p className="text-sm text-gray-500">{doc.specialty_ar} • {doc.dept}</p>
                      <div className="flex flex-wrap gap-3 mt-3 text-sm">
                        <span className="flex items-center gap-1 text-amber-500 font-semibold">
                          <Star className="h-4 w-4 fill-current" /> {doc.rating} ({doc.reviews} تقييم)
                        </span>
                        <span className="text-gray-400">|</span>
                        <span className="text-gray-500">{doc.patients} مريض</span>
                        <span className="text-gray-400">|</span>
                        <span className="text-teal-600 font-bold">{doc.fee} ر.س</span>
                      </div>
                      <div className="flex items-center gap-1 mt-2 text-xs text-gray-500">
                        <Clock className="h-3 w-3" /> {doc.schedule}
                      </div>
                    </div>
                    <Button
                      className="bg-gradient-to-r from-teal-500 to-emerald-600 text-white hover:from-teal-600 hover:to-emerald-700"
                      disabled={!doc.available}
                      onClick={() => navigate('booking', { doctorId: doc.id })}
                    >
                      <Calendar className="h-4 w-4 ml-1" />
                      احجز موعد
                    </Button>
                  </div>
                </CardContent>
              </Card>

              {/* Reviews for this doctor */}
              <Card>
                <CardHeader>
                  <CardTitle className="text-sm">تقييمات المرضى</CardTitle>
                </CardHeader>
                <CardContent className="space-y-3">
                  {reviews.map((r) => (
                    <div key={r.id} className="flex gap-3 p-3 rounded-lg border">
                      <div className="flex-1">
                        <div className="flex items-center gap-2 mb-1">
                          <span className="text-sm font-semibold">{r.patient}</span>
                          <div className="flex gap-0.5">
                            {Array.from({ length: r.rating }).map((_, i) => (
                              <Star key={i} className="h-2.5 w-2.5 fill-amber-400 text-amber-400" />
                            ))}
                          </div>
                        </div>
                        <p className="text-xs text-gray-600">{r.comment}</p>
                      </div>
                      <span className="text-[10px] text-gray-400 shrink-0">{r.date}</span>
                    </div>
                  ))}
                </CardContent>
              </Card>
            </div>
          )
        })()}

        {/* DEPARTMENTS PAGE */}
        {currentPage === 'departments' && (
          <div className="space-y-6">
            <div className="flex items-center gap-3">
              <Button variant="ghost" size="sm" onClick={() => navigate('home')}><ArrowLeft className="h-4 w-4" /></Button>
              <h2 className="text-xl font-bold">الأقسام الطبية</h2>
            </div>
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
              {departments.map((dept) => (
                <Card key={dept.id} className="hover:shadow-md transition-shadow cursor-pointer" onClick={() => navigate('doctors')}>
                  <CardContent className="p-5 text-center">
                    <dept.icon className={`h-12 w-12 mx-auto mb-3 ${dept.color}`} />
                    <h3 className="font-bold">{dept.name_ar}</h3>
                    <p className="text-[10px] text-gray-400 mb-2">{dept.name_en}</p>
                    <p className="text-xs text-gray-500">{dept.desc_ar}</p>
                    <p className="text-xs text-teal-600 mt-3 font-semibold">{dept.doctors} أطباء</p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        )}

        {/* BOOKING PAGE */}
        {currentPage === 'booking' && selectedDoctor > 0 && (() => {
          const doc = doctors.find((d) => d.id === selectedDoctor) || doctors[0]
          return (
            <div className="max-w-2xl mx-auto space-y-6">
              <Button variant="ghost" size="sm" onClick={() => navigate('doctor-profile', { doctorId: doc.id })}>
                <ArrowLeft className="h-4 w-4 ml-1" /> العودة لملف الطبيب
              </Button>

              <Card>
                <CardHeader>
                  <CardTitle>حجز موعد جديد</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {bookingSuccess ? (
                    <div className="text-center py-8">
                      <CheckCircle2 className="h-16 w-16 text-green-500 mx-auto mb-4" />
                      <h3 className="text-xl font-bold text-green-600 mb-2">تم الحجز بنجاح!</h3>
                      <p className="text-sm text-gray-500 mb-4">
                        تم حجز موعدك مع {doc.name_ar} بتاريخ {selectedDate} الساعة {selectedTime}
                      </p>
                      <p className="text-xs text-gray-400 mb-6">رقم الطابور سيُرسل لك عبر SMS والإشعارات</p>
                      <div className="flex gap-3 justify-center">
                        <Button onClick={() => navigate('queue')} variant="outline">تتبع الطابور</Button>
                        <Button onClick={() => navigate('home')} className="bg-teal-500 text-white">الرئيسية</Button>
                      </div>
                    </div>
                  ) : (
                    <>
                      {/* Doctor Info */}
                      <div className="flex items-center gap-3 p-3 rounded-lg bg-teal-50">
                        <span className="text-2xl">{doc.image}</span>
                        <div>
                          <p className="font-semibold text-sm">{doc.name_ar}</p>
                          <p className="text-xs text-gray-500">{doc.specialty_ar} • {doc.fee} ر.س</p>
                        </div>
                      </div>

                      {/* Date Selection */}
                      <div>
                        <label className="text-sm font-semibold mb-2 block">تاريخ الموعد</label>
                        <Input
                          type="date"
                          min={new Date().toISOString().split('T')[0]}
                          value={selectedDate}
                          onChange={(e) => setSelectedDate(e.target.value)}
                          className="rounded-lg"
                        />
                      </div>

                      {/* Time Selection */}
                      {selectedDate && (
                        <div>
                          <label className="text-sm font-semibold mb-2 block">وقت الموعد</label>
                          <div className="grid grid-cols-3 sm:grid-cols-6 gap-2">
                            {timeSlots.map((slot) => (
                              <button
                                key={slot}
                                className={`p-2 rounded-lg text-xs font-medium border transition-colors ${
                                  selectedTime === slot
                                    ? 'bg-teal-500 text-white border-teal-500'
                                    : 'bg-white hover:bg-teal-50 hover:border-teal-200'
                                }`}
                                onClick={() => setSelectedTime(slot)}
                              >
                                {slot}
                              </button>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* Payment Method */}
                      {selectedTime && (
                        <div>
                          <label className="text-sm font-semibold mb-2 block">طريقة الدفع</label>
                          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            {[
                              { label: 'نقداً', icon: '💵' },
                              { label: 'بطاقة', icon: '💳' },
                              { label: 'محفظة', icon: '📱' },
                              { label: 'تأمين', icon: '🏥' },
                            ].map((m) => (
                              <button
                                key={m.label}
                                className="flex items-center gap-2 p-3 rounded-lg border hover:bg-teal-50 hover:border-teal-200 transition-colors text-xs"
                              >
                                <span className="text-lg">{m.icon}</span>
                                {m.label}
                              </button>
                            ))}
                          </div>
                        </div>
                      )}

                      {/* Summary */}
                      {selectedTime && (
                        <div className="rounded-lg border p-4 bg-gray-50">
                          <div className="flex justify-between text-sm mb-1">
                            <span className="text-gray-500">الطبيب:</span>
                            <span className="font-semibold">{doc.name_ar}</span>
                          </div>
                          <div className="flex justify-between text-sm mb-1">
                            <span className="text-gray-500">التاريخ:</span>
                            <span className="font-semibold">{selectedDate}</span>
                          </div>
                          <div className="flex justify-between text-sm mb-1">
                            <span className="text-gray-500">الوقت:</span>
                            <span className="font-semibold">{selectedTime}</span>
                          </div>
                          <Separator className="my-2" />
                          <div className="flex justify-between text-sm">
                            <span className="text-gray-500">الرسوم:</span>
                            <span className="font-bold text-teal-600">{doc.fee} ر.س</span>
                          </div>
                        </div>
                      )}

                      <Button
                        className="w-full bg-gradient-to-r from-teal-500 to-emerald-600 text-white hover:from-teal-600 hover:to-emerald-700 h-11"
                        disabled={!selectedDate || !selectedTime}
                        onClick={handleBooking}
                      >
                        <Calendar className="h-4 w-4 ml-2" />
                        تأكيد الحجز
                      </Button>
                    </>
                  )}
                </CardContent>
              </Card>
            </div>
          )
        })()}

        {/* LOGIN PAGE */}
        {currentPage === 'login' && (
          <div className="max-w-md mx-auto">
            <Card>
              <CardHeader className="text-center">
                <span className="text-4xl mb-2 block">🏥</span>
                <CardTitle>تسجيل الدخول</CardTitle>
                <p className="text-xs text-gray-500">ادخل إلى حسابك في MediCare Pro</p>
              </CardHeader>
              <CardContent className="space-y-4">
                <div>
                  <label className="text-sm font-medium mb-1 block">البريد الإلكتروني</label>
                  <Input type="email" placeholder="example@email.com" className="rounded-lg" dir="ltr" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">كلمة المرور</label>
                  <Input type="password" placeholder="••••••••" className="rounded-lg" dir="ltr" />
                </div>
                <Button className="w-full bg-gradient-to-r from-teal-500 to-emerald-600 text-white h-11">تسجيل الدخول</Button>
                <p className="text-center text-xs text-gray-500">
                  ليس لديك حساب؟ <button className="text-teal-600 font-semibold" onClick={() => navigate('register')}>إنشاء حساب</button>
                </p>
              </CardContent>
            </Card>
          </div>
        )}

        {/* REGISTER PAGE */}
        {currentPage === 'register' && (
          <div className="max-w-md mx-auto">
            <Card>
              <CardHeader className="text-center">
                <span className="text-4xl mb-2 block">🤒</span>
                <CardTitle>إنشاء حساب جديد</CardTitle>
                <p className="text-xs text-gray-500">سجل كي تحجز مواعيد وتتابع سجلاتك الطبية</p>
              </CardHeader>
              <CardContent className="space-y-3">
                <div>
                  <label className="text-sm font-medium mb-1 block">الاسم الكامل</label>
                  <Input placeholder="الاسم الثلاثي" className="rounded-lg" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">البريد الإلكتروني</label>
                  <Input type="email" placeholder="example@email.com" className="rounded-lg" dir="ltr" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">رقم الهاتف</label>
                  <Input placeholder="+9665XXXXXXXX" className="rounded-lg" dir="ltr" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">كلمة المرور</label>
                  <Input type="password" placeholder="8 أحرف على الأقل" className="rounded-lg" dir="ltr" />
                </div>
                <div>
                  <label className="text-sm font-medium mb-1 block">المستشفى المفضل</label>
                  <select className="w-full border rounded-lg p-2 text-sm">
                    {hospitals.map((h) => (
                      <option key={h.id} value={h.id}>{h.name_ar}</option>
                    ))}
                  </select>
                </div>
                <Button className="w-full bg-gradient-to-r from-teal-500 to-emerald-600 text-white h-11">إنشاء الحساب</Button>
                <p className="text-center text-xs text-gray-500">
                  لديك حساب؟ <button className="text-teal-600 font-semibold" onClick={() => navigate('login')}>تسجيل الدخول</button>
                </p>
              </CardContent>
            </Card>
          </div>
        )}

        {/* QUEUE STATUS PAGE */}
        {currentPage === 'queue' && (
          <div className="max-w-lg mx-auto space-y-4">
            <div className="flex items-center gap-3">
              <Button variant="ghost" size="sm" onClick={() => navigate('home')}><ArrowLeft className="h-4 w-4" /></Button>
              <h2 className="text-xl font-bold">حالة الطابور</h2>
            </div>
            <Card>
              <CardContent className="p-6 text-center">
                <div className="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-teal-50 text-4xl mb-4">
                  <TicketCheck className="h-10 w-10 text-teal-500" />
                </div>
                <p className="text-sm text-gray-500 mb-1">رقمك في الطابور</p>
                <p className="text-4xl font-bold text-teal-600 mb-4">D1-015</p>
                <div className="grid grid-cols-3 gap-3 mb-4">
                  <div className="rounded-lg bg-gray-50 p-3">
                    <p className="text-xs text-gray-500">يُخدم الآن</p>
                    <p className="text-lg font-bold text-blue-600">D1-012</p>
                  </div>
                  <div className="rounded-lg bg-gray-50 p-3">
                    <p className="text-xs text-gray-500">المتقدمون</p>
                    <p className="text-lg font-bold text-amber-600">3</p>
                  </div>
                  <div className="rounded-lg bg-gray-50 p-3">
                    <p className="text-xs text-gray-500">الانتظار المقدر</p>
                    <p className="text-lg font-bold">~22 د</p>
                  </div>
                </div>
                <div className="rounded-lg bg-amber-50 p-3 text-amber-700 text-xs">
                  ⏰ سيتم إرسال إشعار عند اقتراب دورك
                </div>
              </CardContent>
            </Card>
          </div>
        )}

        {/* HOSPITAL PAGE */}
        {currentPage === 'hospital' && (
          <div className="space-y-6">
            <div className="flex items-center gap-3">
              <Button variant="ghost" size="sm" onClick={() => navigate('home')}><ArrowLeft className="h-4 w-4" /></Button>
              <h2 className="text-xl font-bold">{hospitals.find((h) => h.id === selectedHospital)?.name_ar}</h2>
            </div>
            <div className="grid sm:grid-cols-2 gap-4">
              <div className="rounded-xl bg-gradient-to-l from-teal-500/10 to-emerald-500/10 p-8 text-center">
                <span className="text-6xl">{hospitals.find((h) => h.id === selectedHospital)?.image}</span>
                <p className="mt-3 text-sm text-gray-500 flex items-center justify-center gap-1">
                  <MapPin className="h-3 w-3" /> {hospitals.find((h) => h.id === selectedHospital)?.location}
                </p>
              </div>
              <Card>
                <CardContent className="p-6 space-y-3">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">الأطباء</span>
                    <span className="font-bold">{hospitals.find((h) => h.id === selectedHospital)?.doctors}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">الأقسام</span>
                    <span className="font-bold">{hospitals.find((h) => h.id === selectedHospital)?.departments}</span>
                  </div>
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-500">التقييم</span>
                    <span className="font-bold text-amber-500 flex items-center gap-1">
                      <Star className="h-3.5 w-3.5 fill-current" /> {hospitals.find((h) => h.id === selectedHospital)?.rating}
                    </span>
                  </div>
                  <Separator />
                  <Button className="w-full bg-teal-500 text-white" onClick={() => navigate('doctors')}>
                    حجز موعد الآن
                  </Button>
                </CardContent>
              </Card>
            </div>
          </div>
        )}

        {/* PROFILE PAGE */}
        {currentPage === 'profile' && (
          <div className="max-w-2xl mx-auto space-y-4">
            <h2 className="text-xl font-bold">الملف الشخصي</h2>
            <Card>
              <CardContent className="p-6">
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-16 h-16 rounded-full bg-teal-100 flex items-center justify-center text-2xl">🤒</div>
                  <div>
                    <h3 className="font-bold">أحمد محمد علي</h3>
                    <p className="text-xs text-gray-500">ahmed@example.com • +966501234567</p>
                  </div>
                </div>
                <div className="grid sm:grid-cols-2 gap-4">
                  {[
                    { label: 'فصيلة الدم', value: 'A+', icon: '🩸' },
                    { label: 'تاريخ الميلاد', value: '1990-05-15', icon: '🎂' },
                    { label: 'الجنس', value: 'ذكر', icon: '👤' },
                    { label: 'العنوان', value: 'الرياض - حي العليا', icon: '📍' },
                  ].map((field) => (
                    <div key={field.label} className="flex items-center gap-2 p-3 rounded-lg border">
                      <span>{field.icon}</span>
                      <div>
                        <p className="text-[10px] text-gray-400">{field.label}</p>
                        <p className="text-sm font-semibold">{field.value}</p>
                      </div>
                    </div>
                  ))}
                </div>
                <div className="mt-4 flex gap-3">
                  <Button className="flex-1 bg-teal-500 text-white" onClick={() => navigate('records')}>
                    <FileText className="h-4 w-4 ml-1" /> السجلات الطبية
                  </Button>
                  <Button className="flex-1 bg-teal-500 text-white" onClick={() => navigate('prescriptions')}>
                    <Pill className="h-4 w-4 ml-1" /> الوصفات
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        )}

        {/* RECORDS PAGE */}
        {currentPage === 'records' && (
          <div className="max-w-2xl mx-auto space-y-4">
            <Button variant="ghost" size="sm" onClick={() => navigate('profile')}><ArrowLeft className="h-4 w-4 ml-1" /> الملف الشخصي</Button>
            <h2 className="text-xl font-bold">السجلات الطبية</h2>
            {[
              { id: 1, date: '2026-08-14', doctor: 'د. سارة علي', diagnosis: 'أنفلونزا موسمية', dept: 'الباطنة' },
              { id: 2, date: '2026-07-20', doctor: 'د. خالد يوسف', diagnosis: 'ارتفاع ضغط الدم', dept: 'القلب' },
              { id: 3, date: '2026-06-10', doctor: 'د. منى عبدالله', diagnosis: 'التهاب جلد', dept: 'الجلدية' },
            ].map((r) => (
              <Card key={r.id}>
                <CardContent className="p-4">
                  <div className="flex justify-between items-start">
                    <div>
                      <p className="font-semibold text-sm">{r.diagnosis}</p>
                      <p className="text-xs text-gray-500 mt-1">{r.doctor} • {r.dept}</p>
                    </div>
                    <span className="text-xs text-gray-400">{r.date}</span>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}

        {/* PRESCRIPTIONS PAGE */}
        {currentPage === 'prescriptions' && (
          <div className="max-w-2xl mx-auto space-y-4">
            <Button variant="ghost" size="sm" onClick={() => navigate('profile')}><ArrowLeft className="h-4 w-4 ml-1" /> الملف الشخصي</Button>
            <h2 className="text-xl font-bold">الوصفات الطبية</h2>
            {[
              { id: 1, date: '2026-08-14', doctor: 'د. سارة علي', items: ['باراسيتامول 500ملغ - 3 مرات يومياً', 'فيتامين C 1000ملغ - مرة يومياً'], status: 'مكتملة' },
              { id: 2, date: '2026-07-20', doctor: 'د. خالد يوسف', items: ['أملوديبين 5ملغ - مرة يومياً'], status: 'مكتملة' },
            ].map((p) => (
              <Card key={p.id}>
                <CardContent className="p-4">
                  <div className="flex justify-between items-start mb-3">
                    <div>
                      <p className="font-semibold text-sm">وصفة #{p.id}</p>
                      <p className="text-xs text-gray-500">{p.doctor} • {p.date}</p>
                    </div>
                    <Badge className="bg-green-50 text-green-600">{p.status}</Badge>
                  </div>
                  <div className="space-y-1">
                    {p.items.map((item, i) => (
                      <p key={i} className="text-xs text-gray-600 flex items-center gap-1">
                        <Pill className="h-3 w-3" /> {item}
                      </p>
                    ))}
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </main>

      {/* ═══════════════════════════════════════════════════════ */}
      {/* FOOTER */}
      {/* ═══════════════════════════════════════════════════════ */}
      <footer className="mt-12 border-t bg-white">
        <div className="mx-auto max-w-6xl px-4 py-8">
          <div className="grid sm:grid-cols-3 gap-6">
            <div>
              <div className="flex items-center gap-2 mb-3">
                <span className="text-xl">🏥</span>
                <span className="font-bold text-teal-700">MediCare Pro</span>
              </div>
              <p className="text-xs text-gray-500">نظام متكامل لإدارة المستشفيات والعيادات - رعاية صحية أفضل للجميع</p>
            </div>
            <div>
              <h4 className="font-semibold text-sm mb-2">روابط سريعة</h4>
              <div className="space-y-1">
                <button onClick={() => navigate('home')} className="block text-xs text-gray-500 hover:text-teal-600">الرئيسية</button>
                <button onClick={() => navigate('doctors')} className="block text-xs text-gray-500 hover:text-teal-600">الأطباء</button>
                <button onClick={() => navigate('departments')} className="block text-xs text-gray-500 hover:text-teal-600">الأقسام</button>
              </div>
            </div>
            <div>
              <h4 className="font-semibold text-sm mb-2">تواصل معنا</h4>
              <div className="space-y-1">
                <p className="text-xs text-gray-500 flex items-center gap-1"><Phone className="h-3 w-3" /> 920012345</p>
                <p className="text-xs text-gray-500 flex items-center gap-1"><Mail className="h-3 w-3" /> info@medicare.com</p>
              </div>
            </div>
          </div>
          <Separator className="my-6" />
          <div className="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400">
            <p>© 2026 MediCare Pro. جميع الحقوق محفوظة.</p>
            <div className="flex items-center gap-3">
              <span>Laravel 11 + Flutter</span>
              <span>•</span>
              <span>80+ API Endpoints</span>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

/* ═══════════════════════════════════════════════════════════ */
/* Sub-components */
/* ═══════════════════════════════════════════════════════════ */

function NavButton({ children, onClick, active }: { children: React.ReactNode; onClick: () => void; active?: boolean }) {
  return (
    <button
      onClick={onClick}
      className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
        active ? 'bg-teal-50 text-teal-700' : 'text-gray-600 hover:bg-gray-100'
      }`}
    >
      {children}
    </button>
  )
}

function MobileNavButton({ children, onClick }: { children: React.ReactNode; onClick: () => void }) {
  return (
    <button
      onClick={onClick}
      className="w-full text-right px-3 py-2.5 rounded-lg text-sm hover:bg-gray-100 transition-colors"
    >
      {children}
    </button>
  )
}
