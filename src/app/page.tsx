'use client'

import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import { Skeleton } from '@/components/ui/skeleton'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
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
  CheckCircle2,
  Globe,
  Heart,
  Users,
  Stethoscope,
  Baby,
  Eye,
  Bone,
  Pill,
  Menu,
  X,
  ArrowLeft,
  Shield,
  Building2,
  UserCheck,
  Award,
  Quote,
  Activity,
  LogIn,
  LogOut,
  BriefcaseMedical,
  LayoutDashboard,
  BarChart3,
  UserCog,
  ClipboardList,
  Settings,
  Trash2,
  Edit,
  Plus,
  TrendingUp,
  DollarSign,
  AlertCircle,
  ChevronDown,
} from 'lucide-react'

// ═══════════════════════════════════════════════════════════════
// TYPES
// ═══════════════════════════════════════════════════════════════

interface HospitalData {
  id: string
  name: string
  nameEn: string
  location: string
  locationEn: string
  phone: string
  email: string
  rating: number
  departments: number
  doctors: number
  image: string
  active: boolean
  departmentsList: DepartmentData[]
  doctorsList: DoctorData[]
}

interface DepartmentData {
  id: string
  name: string
  nameEn: string
  icon: string
  hospitalId: string
  active: boolean
}

interface DoctorData {
  id: string
  name: string
  nameEn: string
  specialty: string
  specialtyEn: string
  phone: string
  email: string
  rating: number
  experience: number
  price: number
  image: string
  bio: string
  bioEn: string
  hospitalId: string
  departmentId: string
  available: boolean
  hospital: HospitalData
  department: DepartmentData
  schedules: ScheduleData[]
}

interface ScheduleData {
  id: string
  day: string
  startTime: string
  endTime: string
  doctorId: string
}

interface AppointmentData {
  id: string
  patientName: string
  patientPhone: string
  patientEmail: string
  date: string
  time: string
  status: string
  notes: string
  doctorId: string
  hospitalId: string
  doctor: DoctorData
  createdAt: string
}

// ═══════════════════════════════════════════════════════════════
// TRANSLATIONS
// ═══════════════════════════════════════════════════════════════

const t = {
  ar: {
    siteName: 'MediCare Pro',
    home: 'الرئيسية',
    hospitals: 'المستشفيات',
    doctors: 'الأطباء',
    bookAppointment: 'حجز موعد',
    myAppointments: 'مواعيدي',
    login: 'تسجيل الدخول',
    logout: 'تسجيل الخروج',
    langSwitch: 'EN',
    heroTitle: 'منصة ميدي كير الطبية',
    heroSubtitle: 'نقدم لكم أفضل الخدمات الطبية مع نخبة من الأطباء المتخصصين في مختلف المجالات الطبية. احجز موعدك بسهولة وأمان مع ميدي كير برو.',
    heroCta1: 'احجز موعدك الآن',
    heroCta2: 'تصفح الأطباء',
    statHospitals: 'مستشفيات',
    statDoctors: 'أطباء',
    statPatients: 'مريض',
    statDepartments: 'تخصص',
    servicesTitle: 'خدماتنا الطبية',
    servicesSubtitle: 'نوفر لكم مجموعة واسعة من التخصصات الطبية المتنوعة',
    cardiology: 'طب القلب والأوعية الدموية',
    orthopedics: 'جراحة العظام',
    pediatrics: 'طب الأطفال',
    ophthalmology: 'طب العيون',
    generalMedicine: 'الطب الباطني',
    pharmacy: 'الصيدلة',
    hospitalsTitle: 'مستشفياتنا',
    hospitalsSubtitle: 'اختر من أفضل المستشفيات الشريكة لنا',
    viewDetails: 'عرض التفاصيل',
    doctorsTitle: 'أطباؤنا المتخصصون',
    doctorsSubtitle: 'نخبة من أفضل الأطباء في المملكة',
    bookNow: 'احجز الآن',
    experience: 'سنة خبرة',
    sar: 'ر.س',
    reviews: 'تقييم',
    testimonialsTitle: 'آراء المرضى',
    testimonialsSubtitle: 'ماذا يقول مرضانا عن تجربتهم معنا',
    testimonial1: 'تجربة ممتازة! الحجز كان سهل جداً والدكتور أحمد محترف للغاية. أنصح الجميع باستخدام المنصة.',
    testimonial1Name: 'محمد أحمد',
    testimonial1Title: 'مريض - الرياض',
    testimonial2: 'خدمة رائعة وسريعة. استطعت حجز موعد مع أفضل طبيب عظام في وقت قصير جداً.',
    testimonial2Name: 'فاطمة علي',
    testimonial2Title: 'مريضة - جدة',
    testimonial3: 'منصة سهلة الاستخدام ومريحة جداً. الأطباء متخصصون والأسعار مناسبة.',
    testimonial3Name: 'خالد العمري',
    testimonial3Title: 'مريض - الدمام',
    footerDesc: 'منصة طبية متكاملة تهدف إلى تقديم أفضل الخدمات الصحية للمرضى في المملكة العربية السعودية.',
    footerLinks: 'روابط سريعة',
    footerContact: 'تواصل معنا',
    footerRights: 'جميع الحقوق محفوظة',
    searchHospitals: 'ابحث عن مستشفى...',
    searchDoctors: 'ابحث عن طبيب...',
    filterByHospital: 'تصفية حسب المستشفى',
    filterByDept: 'تصفية حسب التخصص',
    allHospitals: 'جميع المستشفيات',
    allDepartments: 'جميع التخصصات',
    back: 'رجوع',
    hospitalInfo: 'معلومات المستشفى',
    departments: 'الأقسام',
    doctorsInHospital: 'الأطباء في هذا المستشفى',
    doctorInfo: 'معلومات الطبيب',
    bio: 'نبذة عن الطبيب',
    weeklySchedule: 'الجدول الأسبوعي',
    day: 'اليوم',
    time: 'الوقت',
    available: 'متاح',
    selectHospital: 'اختر المستشفى',
    selectDoctor: 'اختر الطبيب',
    selectDate: 'اختر التاريخ',
    selectTime: 'اختر الوقت',
    patientName: 'اسم المريض',
    patientPhone: 'رقم الهاتف',
    patientEmail: 'البريد الإلكتروني',
    notes: 'ملاحظات (اختياري)',
    submitBooking: 'تأكيد الحجز',
    bookingSuccess: 'تم حجز الموعد بنجاح!',
    bookingSuccessMsg: 'سيتم التواصل معك قريباً لتأكيد الموعد.',
    bookingTitle: 'حجز موعد جديد',
    email: 'البريد الإلكتروني',
    password: 'كلمة المرور',
    loginBtn: 'دخول',
    loginTitle: 'تسجيل الدخول',
    loginSubtitle: 'أدخل بياناتك للوصول إلى حسابك',
    demoNote: 'ملاحظة: هذا نسخة تجريبية، أي بيانات دخول ستعمل.',
    adminNote: 'لوحة تحكم المسؤول متاحة على منفذ منفصل.',
    goToHome: 'العودة للرئيسية',
    noResults: 'لا توجد نتائج',
    loading: 'جاري التحميل...',
    location: 'الموقع',
    phone: 'الهاتف',
    contact: 'التواصل',
    department: 'القسم',
    schedule: 'الجدول',
    price: 'السعر',
    status: 'الحالة',
    pending: 'قيد الانتظار',
    confirmed: 'مؤكد',
    cancelled: 'ملغي',
    completed: 'مكتمل',
    allStatuses: 'جميع الحالات',
    appointmentDate: 'تاريخ الموعد',
    appointmentTime: 'وقت الموعد',
    doctorName: 'اسم الطبيب',
    hospitalName: 'المستشفى',
    appointmentDetails: 'تفاصيل الموعد',
    filterByStatus: 'تصفية حسب الحالة',
    sunday: 'الأحد',
    monday: 'الاثنين',
    tuesday: 'الثلاثاء',
    wednesday: 'الأربعاء',
    thursday: 'الخميس',
    friday: 'الجمعة',
    saturday: 'السبت',
    sundayEn: 'الأحد',
    mondayEn: 'الاثنين',
    tuesdayEn: 'الثلاثاء',
    wednesdayEn: 'الأربعاء',
    thursdayEn: 'الخميس',
    fridayEn: 'الجمعة',
    saturdayEn: 'السبت',
    phoneContact: '+966-800-123-4567',
    emailContact: 'info@medicare.sa',
  },
  en: {
    siteName: 'MediCare Pro',
    home: 'Home',
    hospitals: 'Hospitals',
    doctors: 'Doctors',
    bookAppointment: 'Book Appointment',
    myAppointments: 'My Appointments',
    login: 'Login',
    logout: 'Logout',
    langSwitch: 'عربي',
    heroTitle: 'MediCare Pro Medical Platform',
    heroSubtitle: 'We provide the best medical services with a selection of specialized doctors across various medical fields. Book your appointment easily and securely with MediCare Pro.',
    heroCta1: 'Book Your Appointment Now',
    heroCta2: 'Browse Doctors',
    statHospitals: 'Hospitals',
    statDoctors: 'Doctors',
    statPatients: 'Patients',
    statDepartments: 'Departments',
    servicesTitle: 'Our Medical Services',
    servicesSubtitle: 'We offer a wide range of diverse medical specialties',
    cardiology: 'Cardiology & Vascular',
    orthopedics: 'Orthopedics',
    pediatrics: 'Pediatrics',
    ophthalmology: 'Ophthalmology',
    generalMedicine: 'General Medicine',
    pharmacy: 'Pharmacy',
    hospitalsTitle: 'Our Hospitals',
    hospitalsSubtitle: 'Choose from our top partner hospitals',
    viewDetails: 'View Details',
    doctorsTitle: 'Our Specialist Doctors',
    doctorsSubtitle: 'A selection of the best doctors in the kingdom',
    bookNow: 'Book Now',
    experience: 'Years Exp.',
    sar: 'SAR',
    reviews: 'Rating',
    testimonialsTitle: 'Patient Reviews',
    testimonialsSubtitle: 'What our patients say about their experience',
    testimonial1: 'Excellent experience! Booking was very easy and Dr. Ahmed is extremely professional. I recommend everyone to use this platform.',
    testimonial1Name: 'Mohammed Ahmed',
    testimonial1Title: 'Patient - Riyadh',
    testimonial2: 'Great and fast service. I was able to book an appointment with the best orthopedic doctor in a very short time.',
    testimonial2Name: 'Fatima Ali',
    testimonial2Title: 'Patient - Jeddah',
    testimonial3: 'Easy to use and very convenient platform. The doctors are specialized and the prices are reasonable.',
    testimonial3Name: 'Khalid Al-Omari',
    testimonial3Title: 'Patient - Dammam',
    footerDesc: 'An integrated medical platform aiming to provide the best healthcare services to patients in Saudi Arabia.',
    footerLinks: 'Quick Links',
    footerContact: 'Contact Us',
    footerRights: 'All Rights Reserved',
    searchHospitals: 'Search hospitals...',
    searchDoctors: 'Search doctors...',
    filterByHospital: 'Filter by Hospital',
    filterByDept: 'Filter by Department',
    allHospitals: 'All Hospitals',
    allDepartments: 'All Departments',
    back: 'Back',
    hospitalInfo: 'Hospital Information',
    departments: 'Departments',
    doctorsInHospital: 'Doctors in this Hospital',
    doctorInfo: 'Doctor Information',
    bio: 'About the Doctor',
    weeklySchedule: 'Weekly Schedule',
    day: 'Day',
    time: 'Time',
    available: 'Available',
    selectHospital: 'Select Hospital',
    selectDoctor: 'Select Doctor',
    selectDate: 'Select Date',
    selectTime: 'Select Time',
    patientName: 'Patient Name',
    patientPhone: 'Phone Number',
    patientEmail: 'Email Address',
    notes: 'Notes (Optional)',
    submitBooking: 'Confirm Booking',
    bookingSuccess: 'Appointment Booked Successfully!',
    bookingSuccessMsg: 'We will contact you soon to confirm the appointment.',
    bookingTitle: 'Book New Appointment',
    email: 'Email',
    password: 'Password',
    loginBtn: 'Sign In',
    loginTitle: 'Sign In',
    loginSubtitle: 'Enter your credentials to access your account',
    demoNote: 'Note: This is a demo version, any login credentials will work.',
    adminNote: 'Admin Dashboard is available on a separate port.',
    goToHome: 'Back to Home',
    noResults: 'No results found',
    loading: 'Loading...',
    location: 'Location',
    phone: 'Phone',
    contact: 'Contact',
    department: 'Department',
    schedule: 'Schedule',
    price: 'Price',
    status: 'Status',
    pending: 'Pending',
    confirmed: 'Confirmed',
    cancelled: 'Cancelled',
    completed: 'Completed',
    allStatuses: 'All Statuses',
    appointmentDate: 'Appointment Date',
    appointmentTime: 'Appointment Time',
    doctorName: 'Doctor Name',
    hospitalName: 'Hospital',
    appointmentDetails: 'Appointment Details',
    filterByStatus: 'Filter by Status',
    sunday: 'Sunday',
    monday: 'Monday',
    tuesday: 'Tuesday',
    wednesday: 'Wednesday',
    thursday: 'Thursday',
    friday: 'Friday',
    saturday: 'Saturday',
    sundayEn: 'Sunday',
    mondayEn: 'Monday',
    tuesdayEn: 'Tuesday',
    wednesdayEn: 'Wednesday',
    thursdayEn: 'Thursday',
    fridayEn: 'Friday',
    saturdayEn: 'Saturday',
    phoneContact: '+966-800-123-4567',
    emailContact: 'info@medicare.sa',
  },
}

// ═══════════════════════════════════════════════════════════════
// MAIN PAGE COMPONENT
// ═══════════════════════════════════════════════════════════════

export default function MediCarePage() {
  // ── State ──
  const [lang, setLang] = useState<'ar' | 'en'>('ar')
  const [page, setPage] = useState('home')
  const [selectedHospitalId, setSelectedHospitalId] = useState<string | null>(null)
  const [selectedDoctorId, setSelectedDoctorId] = useState<string | null>(null)
  const [hospitals, setHospitals] = useState<HospitalData[]>([])
  const [doctors, setDoctors] = useState<DoctorData[]>([])
  const [appointments, setAppointments] = useState<AppointmentData[]>([])
  const [loading, setLoading] = useState(true)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [isAdmin, setIsAdmin] = useState(false)
  const [adminTab, setAdminTab] = useState('overview')
  const [adminStats, setAdminStats] = useState<any>(null)
  const [deleteConfirm, setDeleteConfirm] = useState<string | null>(null)
  const [isLoggedIn, setIsLoggedIn] = useState(false)

  // Booking form state
  const [bookingForm, setBookingForm] = useState({
    patientName: '',
    patientPhone: '',
    patientEmail: '',
    hospitalId: '',
    doctorId: '',
    date: '',
    time: '',
    notes: '',
  })
  const [bookingSuccess, setBookingSuccess] = useState(false)
  const [bookingLoading, setBookingLoading] = useState(false)

  // Login form state
  const [loginForm, setLoginForm] = useState({ email: '', password: '' })

  // Filter states
  const [hospitalSearch, setHospitalSearch] = useState('')
  const [doctorSearch, setDoctorSearch] = useState('')
  const [filterHospitalId, setFilterHospitalId] = useState('all')
  const [filterDepartmentId, setFilterDepartmentId] = useState('all')
  const [appointmentStatusFilter, setAppointmentStatusFilter] = useState('all')

  // Time slots
  const timeSlots = [
    '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
    '12:00', '12:30', '13:00', '13:30', '14:00', '14:30',
    '15:00', '15:30', '16:00', '16:30',
  ]

  // ── RTL/LTR Effect ──
  useEffect(() => {
    document.documentElement.lang = lang
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr'
  }, [lang])

  // ── Data Fetching ──
  useEffect(() => {
    const fetchData = async () => {
      setLoading(true)
      try {
        const [hospitalsRes, doctorsRes, appointmentsRes] = await Promise.all([
          fetch('/api/hospitals'),
          fetch('/api/doctors'),
          fetch('/api/appointments'),
        ])
        const hospitalsData = await hospitalsRes.json()
        const doctorsData = await doctorsRes.json()
        const appointmentsData = await appointmentsRes.json()
        setHospitals(hospitalsData)
        setDoctors(doctorsData)
        setAppointments(appointmentsData)
      } catch (error) {
        console.error('Failed to fetch data:', error)
      } finally {
        setLoading(false)
      }
    }
    fetchData()
  }, [])

  // ── Helper Functions ──
  const isAr = lang === 'ar'
  const tr = t[lang]

  const getDayTranslation = (day: string) => {
    const dayMap: Record<string, string> = {
      Sunday: isAr ? t.ar.sunday : t.en.sunday,
      Monday: isAr ? t.ar.monday : t.en.monday,
      Tuesday: isAr ? t.ar.tuesday : t.en.tuesday,
      Wednesday: isAr ? t.ar.wednesday : t.en.wednesday,
      Thursday: isAr ? t.ar.thursday : t.en.thursday,
      Friday: isAr ? t.ar.friday : t.en.friday,
      Saturday: isAr ? t.ar.saturday : t.en.saturday,
    }
    return dayMap[day] || day
  }

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'confirmed': return 'bg-emerald-100 text-emerald-800 border-emerald-200'
      case 'pending': return 'bg-amber-100 text-amber-800 border-amber-200'
      case 'cancelled': return 'bg-red-100 text-red-800 border-red-200'
      case 'completed': return 'bg-blue-100 text-blue-800 border-blue-200'
      default: return 'bg-gray-100 text-gray-800 border-gray-200'
    }
  }

  const getStatusText = (status: string) => {
    const map: Record<string, string> = {
      confirmed: tr.confirmed,
      pending: tr.pending,
      cancelled: tr.cancelled,
      completed: tr.completed,
    }
    return map[status] || status
  }

  const navigateTo = (p: string, hospitalId?: string | null, doctorId?: string | null) => {
    setPage(p)
    if (hospitalId !== undefined) setSelectedHospitalId(hospitalId || null)
    if (doctorId !== undefined) setSelectedDoctorId(doctorId || null)
    setMobileMenuOpen(false)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const navigateToBooking = (doctorId?: string, hospitalId?: string) => {
    setBookingForm({
      patientName: '',
      patientPhone: '',
      patientEmail: '',
      hospitalId: hospitalId || '',
      doctorId: doctorId || '',
      date: '',
      time: '',
      notes: '',
    })
    setBookingSuccess(false)
    setPage('booking')
    if (doctorId) setSelectedDoctorId(doctorId)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleBooking = async (e: React.FormEvent) => {
    e.preventDefault()
    setBookingLoading(true)
    try {
      const res = await fetch('/api/appointments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(bookingForm),
      })
      if (res.ok) {
        const newAppointment = await res.json()
        setAppointments((prev) => [newAppointment, ...prev])
        setBookingSuccess(true)
        setBookingForm({
          patientName: '',
          patientPhone: '',
          patientEmail: '',
          hospitalId: '',
          doctorId: '',
          date: '',
          time: '',
          notes: '',
        })
      }
    } catch (error) {
      console.error('Booking failed:', error)
    } finally {
      setBookingLoading(false)
    }
  }

  const handleLogin = (e: React.FormEvent) => {
    e.preventDefault()
    setIsLoggedIn(true)
    setPage('home')
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  const handleLogout = () => {
    setIsLoggedIn(false)
    setPage('home')
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  // Get unique departments for filtering
  const allDepartments = doctors.reduce<DepartmentData[]>((acc, doc) => {
    if (doc.department && !acc.find((d) => d.id === doc.department.id)) {
      acc.push(doc.department)
    }
    return acc
  }, [])

  // ── Filtered Data ──
  const filteredHospitals = hospitals.filter((h) => {
    const searchLower = hospitalSearch.toLowerCase()
    if (!searchLower) return true
    return (
      h.name.toLowerCase().includes(searchLower) ||
      h.nameEn.toLowerCase().includes(searchLower) ||
      h.location.toLowerCase().includes(searchLower) ||
      h.locationEn.toLowerCase().includes(searchLower)
    )
  })

  const filteredDoctors = doctors.filter((d) => {
    const searchLower = doctorSearch.toLowerCase()
    const matchesSearch = !searchLower ||
      d.name.toLowerCase().includes(searchLower) ||
      d.nameEn.toLowerCase().includes(searchLower) ||
      d.specialty.toLowerCase().includes(searchLower) ||
      d.specialtyEn.toLowerCase().includes(searchLower)
    const matchesHospital = filterHospitalId === 'all' || d.hospitalId === filterHospitalId
    const matchesDepartment = filterDepartmentId === 'all' || d.departmentId === filterDepartmentId
    return matchesSearch && matchesHospital && matchesDepartment
  })

  const filteredAppointments = appointments.filter((a) => {
    return appointmentStatusFilter === 'all' || a.status === appointmentStatusFilter
  })

  const selectedHospital = hospitals.find((h) => h.id === selectedHospitalId)
  const selectedDoctor = doctors.find((d) => d.id === selectedDoctorId)

  // ═══════════════════════════════════════════════════════════
  // NAVBAR
  // ═══════════════════════════════════════════════════════════

  const renderNavbar = () => (
    <nav className="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <div className="flex items-center gap-2 cursor-pointer" onClick={() => navigateTo('home')}>
            <div className="w-9 h-9 bg-emerald-600 rounded-lg flex items-center justify-center">
              <Activity className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-bold text-emerald-700">MediCare Pro</span>
          </div>

          {/* Desktop Nav */}
          <div className="hidden md:flex items-center gap-1">
            <Button
              variant="ghost"
              className={`text-sm ${page === 'home' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 hover:text-emerald-600'}`}
              onClick={() => navigateTo('home')}
            >
              {tr.home}
            </Button>
            <Button
              variant="ghost"
              className={`text-sm ${page === 'hospitals' || page === 'hospital-detail' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 hover:text-emerald-600'}`}
              onClick={() => navigateTo('hospitals')}
            >
              {tr.hospitals}
            </Button>
            <Button
              variant="ghost"
              className={`text-sm ${page === 'doctors' || page === 'doctor-detail' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 hover:text-emerald-600'}`}
              onClick={() => navigateTo('doctors')}
            >
              {tr.doctors}
            </Button>
            <Button
              variant="ghost"
              className={`text-sm ${page === 'booking' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 hover:text-emerald-600'}`}
              onClick={() => navigateToBooking()}
            >
              {tr.bookAppointment}
            </Button>
            {isLoggedIn && (
              <Button
                variant="ghost"
                className={`text-sm ${page === 'my-appointments' ? 'text-emerald-600 bg-emerald-50' : 'text-gray-600 hover:text-emerald-600'}`}
                onClick={() => navigateTo('my-appointments')}
              >
                {tr.myAppointments}
              </Button>
            )}
          </div>

          {/* Actions */}
          <div className="flex items-center gap-2">
            {/* Language Toggle */}
            <Button
              variant="outline"
              size="sm"
              className="text-xs border-emerald-200 text-emerald-700 hover:bg-emerald-50 gap-1"
              onClick={() => setLang(lang === 'ar' ? 'en' : 'ar')}
            >
              <Globe className="w-3.5 h-3.5" />
              {tr.langSwitch}
            </Button>

            {/* Login/Logout */}
            {isLoggedIn ? (
              <Button
                variant="ghost"
                size="sm"
                className="text-gray-600 hover:text-red-600 gap-1 hidden sm:flex"
                onClick={handleLogout}
              >
                <LogOut className="w-4 h-4" />
                {tr.logout}
              </Button>
            ) : (
              <Button
                size="sm"
                className="bg-emerald-600 hover:bg-emerald-700 text-white gap-1 hidden sm:flex"
                onClick={() => navigateTo('login')}
              >
                <LogIn className="w-4 h-4" />
                {tr.login}
              </Button>
            )}

            {/* Mobile Menu Toggle */}
            <Button
              variant="ghost"
              size="sm"
              className="md:hidden"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </Button>
          </div>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <div className="md:hidden border-t border-gray-100 py-3 space-y-1">
            <Button variant="ghost" className="w-full justify-start text-gray-600" onClick={() => navigateTo('home')}>
              {tr.home}
            </Button>
            <Button variant="ghost" className="w-full justify-start text-gray-600" onClick={() => navigateTo('hospitals')}>
              {tr.hospitals}
            </Button>
            <Button variant="ghost" className="w-full justify-start text-gray-600" onClick={() => navigateTo('doctors')}>
              {tr.doctors}
            </Button>
            <Button variant="ghost" className="w-full justify-start text-gray-600" onClick={() => navigateToBooking()}>
              {tr.bookAppointment}
            </Button>
            {isLoggedIn && (
              <Button variant="ghost" className="w-full justify-start text-gray-600" onClick={() => navigateTo('my-appointments')}>
                {tr.myAppointments}
              </Button>
            )}
            <Separator />
            {isLoggedIn ? (
              <Button variant="ghost" className="w-full justify-start text-red-600" onClick={handleLogout}>
                <LogOut className="w-4 h-4 me-2" />
                {tr.logout}
              </Button>
            ) : (
              <Button className="w-full bg-emerald-600 hover:bg-emerald-700 text-white justify-center" onClick={() => navigateTo('login')}>
                <LogIn className="w-4 h-4 me-2" />
                {tr.login}
              </Button>
            )}
          </div>
        )}
      </div>
    </nav>
  )

  // ═══════════════════════════════════════════════════════════
  // HERO SECTION
  // ═══════════════════════════════════════════════════════════

  const renderHero = () => (
    <section className="relative bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-600 text-white overflow-hidden">
      {/* Background Pattern */}
      <div className="absolute inset-0 opacity-10">
        <div className="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl" />
        <div className="absolute bottom-10 right-10 w-96 h-96 bg-teal-300 rounded-full blur-3xl" />
      </div>

      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
        <div className="text-center max-w-4xl mx-auto">
          <h1 className="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold mb-6 leading-tight">
            {tr.heroTitle}
          </h1>
          <p className="text-lg sm:text-xl text-emerald-100 mb-10 max-w-3xl mx-auto leading-relaxed">
            {tr.heroSubtitle}
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <Button
              size="lg"
              className="bg-white text-emerald-700 hover:bg-emerald-50 font-semibold text-base px-8 py-6 rounded-lg shadow-lg"
              onClick={() => navigateToBooking()}
            >
              <Calendar className="w-5 h-5 me-2" />
              {tr.heroCta1}
            </Button>
            <Button
              size="lg"
              variant="outline"
              className="border-white text-white hover:bg-white/10 font-semibold text-base px-8 py-6 rounded-lg"
              onClick={() => navigateTo('doctors')}
            >
              <Stethoscope className="w-5 h-5 me-2" />
              {tr.heroCta2}
            </Button>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-2 sm:grid-cols-4 gap-6 sm:gap-8 max-w-3xl mx-auto">
            {[
              { value: '3+', label: tr.statHospitals, icon: Hospital },
              { value: '10+', label: tr.statDoctors, icon: Users },
              { value: '1000+', label: tr.statPatients, icon: UserCheck },
              { value: '12', label: tr.statDepartments, icon: Shield },
            ].map((stat, i) => (
              <div key={i} className="text-center">
                <stat.icon className="w-6 h-6 mx-auto mb-2 text-emerald-200" />
                <div className="text-2xl sm:text-3xl font-bold">{stat.value}</div>
                <div className="text-emerald-200 text-sm mt-1">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </section>
  )

  // ═══════════════════════════════════════════════════════════
  // SERVICES SECTION
  // ═══════════════════════════════════════════════════════════

  const renderServices = () => (
    <section className="py-16 sm:py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">{tr.servicesTitle}</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">{tr.servicesSubtitle}</p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          {[
            { icon: Heart, ar: t.ar.cardiology, en: t.en.cardiology, color: 'text-red-500 bg-red-50' },
            { icon: Bone, ar: t.ar.orthopedics, en: t.en.orthopedics, color: 'text-amber-600 bg-amber-50' },
            { icon: Baby, ar: t.ar.pediatrics, en: t.en.pediatrics, color: 'text-pink-500 bg-pink-50' },
            { icon: Eye, ar: t.ar.ophthalmology, en: t.en.ophthalmology, color: 'text-indigo-500 bg-indigo-50' },
            { icon: Stethoscope, ar: t.ar.generalMedicine, en: t.en.generalMedicine, color: 'text-emerald-600 bg-emerald-50' },
            { icon: Pill, ar: t.ar.pharmacy, en: t.en.pharmacy, color: 'text-purple-500 bg-purple-50' },
          ].map((service, i) => (
            <Card
              key={i}
              className="group rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-0 cursor-pointer hover:-translate-y-1"
              onClick={() => navigateTo('doctors')}
            >
              <CardContent className="p-6 text-center">
                <div className={`w-14 h-14 rounded-xl ${service.color} flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300`}>
                  <service.icon className="w-7 h-7" />
                </div>
                <h3 className="font-semibold text-gray-900 text-lg">{isAr ? service.ar : service.en}</h3>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  )

  // ═══════════════════════════════════════════════════════════
  // HOSPITALS SECTION (Home Page)
  // ═══════════════════════════════════════════════════════════

  const renderHospitalsSection = () => (
    <section className="py-16 sm:py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">{tr.hospitalsTitle}</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">{tr.hospitalsSubtitle}</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map((i) => (
              <Card key={i} className="rounded-xl">
                <CardContent className="p-6">
                  <Skeleton className="h-8 w-3/4 mb-4" />
                  <Skeleton className="h-4 w-1/2 mb-2" />
                  <Skeleton className="h-4 w-2/3 mb-4" />
                  <div className="flex gap-2">
                    <Skeleton className="h-6 w-16" />
                    <Skeleton className="h-6 w-16" />
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {hospitals.slice(0, 3).map((hospital) => (
              <Card
                key={hospital.id}
                className="group rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-0 hover:-translate-y-1"
              >
                <CardContent className="p-6">
                  {/* Hospital Header */}
                  <div className="flex items-start gap-3 mb-4">
                    <div className="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                      <Hospital className="w-6 h-6 text-emerald-600" />
                    </div>
                    <div className="min-w-0">
                      <h3 className="font-bold text-gray-900 text-lg truncate">{isAr ? hospital.name : hospital.nameEn}</h3>
                      <p className="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                        <MapPin className="w-3.5 h-3.5 flex-shrink-0" />
                        <span className="truncate">{isAr ? hospital.location : hospital.locationEn}</span>
                      </p>
                    </div>
                  </div>

                  {/* Rating */}
                  <div className="flex items-center gap-1 mb-3">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-4 h-4 ${i < Math.floor(hospital.rating) ? 'text-amber-400 fill-amber-400' : 'text-gray-300'}`}
                      />
                    ))}
                    <span className="text-sm text-gray-600 ms-1">{hospital.rating}</span>
                  </div>

                  {/* Info */}
                  <div className="flex items-center gap-4 text-sm text-gray-500 mb-4">
                    <span className="flex items-center gap-1">
                      <Shield className="w-4 h-4 text-emerald-500" />
                      {hospital.departments} {tr.departments}
                    </span>
                    <span className="flex items-center gap-1">
                      <Users className="w-4 h-4 text-emerald-500" />
                      {hospital.doctors} {tr.doctors}
                    </span>
                  </div>

                  <Button
                    variant="outline"
                    className="w-full border-emerald-200 text-emerald-700 hover:bg-emerald-50"
                    onClick={() => navigateTo('hospital-detail', hospital.id)}
                  >
                    {tr.viewDetails}
                    <ChevronLeft className={`w-4 h-4 ms-1 ${!isAr ? 'rotate-180' : ''}`} />
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        )}

        <div className="text-center mt-10">
          <Button
            variant="outline"
            className="border-emerald-600 text-emerald-700 hover:bg-emerald-50"
            onClick={() => navigateTo('hospitals')}
          >
            {tr.hospitals}
            <ChevronLeft className={`w-4 h-4 ms-1 ${!isAr ? 'rotate-180' : ''}`} />
          </Button>
        </div>
      </div>
    </section>
  )

  // ═══════════════════════════════════════════════════════════
  // DOCTORS SECTION (Home Page)
  // ═══════════════════════════════════════════════════════════

  const renderDoctorsSection = () => (
    <section className="py-16 sm:py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">{tr.doctorsTitle}</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">{tr.doctorsSubtitle}</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map((i) => (
              <Card key={i} className="rounded-xl">
                <CardContent className="p-6">
                  <Skeleton className="h-8 w-3/4 mb-4" />
                  <Skeleton className="h-4 w-1/2 mb-2" />
                  <Skeleton className="h-4 w-2/3 mb-4" />
                </CardContent>
              </Card>
            ))}
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {doctors.slice(0, 6).map((doctor) => (
              <Card
                key={doctor.id}
                className="group rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-0 hover:-translate-y-1"
              >
                <CardContent className="p-6">
                  {/* Doctor Header */}
                  <div className="flex items-start gap-3 mb-4">
                    <div className="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                      <Stethoscope className="w-6 h-6 text-teal-600" />
                    </div>
                    <div className="min-w-0">
                      <h3 className="font-bold text-gray-900 text-lg truncate">{isAr ? doctor.name : doctor.nameEn}</h3>
                      <p className="text-sm text-emerald-600 font-medium">{isAr ? doctor.specialty : doctor.specialtyEn}</p>
                    </div>
                  </div>

                  {/* Rating and Experience */}
                  <div className="flex items-center gap-3 mb-3">
                    <div className="flex items-center gap-1">
                      <Star className="w-4 h-4 text-amber-400 fill-amber-400" />
                      <span className="text-sm font-medium text-gray-700">{doctor.rating}</span>
                    </div>
                    <span className="text-sm text-gray-500">
                      <BriefcaseMedical className="w-3.5 h-3.5 inline me-1" />
                      {doctor.experience} {tr.experience}
                    </span>
                  </div>

                  {/* Hospital */}
                  <p className="text-sm text-gray-500 flex items-center gap-1 mb-4">
                    <Hospital className="w-3.5 h-3.5" />
                    {isAr ? doctor.hospital.name : doctor.hospital.nameEn}
                  </p>

                  {/* Price */}
                  <div className="flex items-center justify-between mb-4">
                    <span className="text-lg font-bold text-emerald-700">
                      {doctor.price} <span className="text-sm font-normal text-gray-500">{tr.sar}</span>
                    </span>
                  </div>

                  <Button
                    className="w-full bg-emerald-600 hover:bg-emerald-700 text-white"
                    onClick={() => navigateTo('doctor-detail', undefined, doctor.id)}
                  >
                    {tr.bookNow}
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        )}

        <div className="text-center mt-10">
          <Button
            variant="outline"
            className="border-emerald-600 text-emerald-700 hover:bg-emerald-50"
            onClick={() => navigateTo('doctors')}
          >
            {tr.doctors}
            <ChevronLeft className={`w-4 h-4 ms-1 ${!isAr ? 'rotate-180' : ''}`} />
          </Button>
        </div>
      </div>
    </section>
  )

  // ═══════════════════════════════════════════════════════════
  // TESTIMONIALS SECTION
  // ═══════════════════════════════════════════════════════════

  const renderTestimonials = () => (
    <section className="py-16 sm:py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">{tr.testimonialsTitle}</h2>
          <p className="text-gray-600 max-w-2xl mx-auto">{tr.testimonialsSubtitle}</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {[
            { text: tr.testimonial1, name: tr.testimonial1Name, title: tr.testimonial1Title },
            { text: tr.testimonial2, name: tr.testimonial2Name, title: tr.testimonial2Title },
            { text: tr.testimonial3, name: tr.testimonial3Name, title: tr.testimonial3Title },
          ].map((testimonial, i) => (
            <Card key={i} className="rounded-xl shadow-md border-0 bg-white">
              <CardContent className="p-6">
                <Quote className="w-8 h-8 text-emerald-200 mb-4" />
                <p className="text-gray-600 leading-relaxed mb-6">{testimonial.text}</p>
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
                    <Users className="w-5 h-5 text-emerald-600" />
                  </div>
                  <div>
                    <p className="font-semibold text-gray-900 text-sm">{testimonial.name}</p>
                    <p className="text-xs text-gray-500">{testimonial.title}</p>
                  </div>
                  <div className="ms-auto flex gap-0.5">
                    {[...Array(5)].map((_, j) => (
                      <Star key={j} className="w-3.5 h-3.5 text-amber-400 fill-amber-400" />
                    ))}
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  )

  // ═══════════════════════════════════════════════════════════
  // FOOTER
  // ═══════════════════════════════════════════════════════════

  const renderFooter = () => (
    <footer className="bg-gray-900 text-gray-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {/* About */}
          <div>
            <div className="flex items-center gap-2 mb-4">
              <div className="w-9 h-9 bg-emerald-600 rounded-lg flex items-center justify-center">
                <Activity className="w-5 h-5 text-white" />
              </div>
              <span className="text-xl font-bold text-white">MediCare Pro</span>
            </div>
            <p className="text-gray-400 text-sm leading-relaxed">{tr.footerDesc}</p>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="font-semibold text-white mb-4">{tr.footerLinks}</h3>
            <ul className="space-y-2">
              {[
                { label: tr.home, action: () => navigateTo('home') },
                { label: tr.hospitals, action: () => navigateTo('hospitals') },
                { label: tr.doctors, action: () => navigateTo('doctors') },
                { label: tr.bookAppointment, action: () => navigateToBooking() },
              ].map((link, i) => (
                <li key={i}>
                  <button
                    onClick={link.action}
                    className="text-gray-400 hover:text-emerald-400 transition-colors text-sm"
                  >
                    {link.label}
                  </button>
                </li>
              ))}
            </ul>
          </div>

          {/* Contact */}
          <div>
            <h3 className="font-semibold text-white mb-4">{tr.footerContact}</h3>
            <div className="space-y-3">
              <p className="flex items-center gap-2 text-sm text-gray-400">
                <Phone className="w-4 h-4 text-emerald-500 flex-shrink-0" />
                {tr.phoneContact}
              </p>
              <p className="flex items-center gap-2 text-sm text-gray-400">
                <Mail className="w-4 h-4 text-emerald-500 flex-shrink-0" />
                {tr.emailContact}
              </p>
              <p className="flex items-center gap-2 text-sm text-gray-400">
                <MapPin className="w-4 h-4 text-emerald-500 flex-shrink-0" />
                {isAr ? 'الرياض، المملكة العربية السعودية' : 'Riyadh, Saudi Arabia'}
              </p>
            </div>
          </div>
        </div>

        <Separator className="my-8 bg-gray-800" />

        <div className="text-center text-sm text-gray-500">
          © {new Date().getFullYear()} MediCare Pro. {tr.footerRights}.
        </div>
      </div>
    </footer>
  )

  // ═══════════════════════════════════════════════════════════
  // HOSPITALS PAGE
  // ═══════════════════════════════════════════════════════════

  const renderHospitalsPage = () => (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold mb-2">{tr.hospitalsTitle}</h1>
          <p className="text-emerald-100">{tr.hospitalsSubtitle}</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Search */}
        <div className="mb-8">
          <div className="relative max-w-md">
            <Search className={`absolute top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 ${isAr ? 'right-3' : 'left-3'}`} />
            <Input
              placeholder={tr.searchHospitals}
              value={hospitalSearch}
              onChange={(e) => setHospitalSearch(e.target.value)}
              className={`${isAr ? 'pr-10' : 'pl-10'} rounded-lg border-gray-200`}
            />
          </div>
        </div>

        {/* Hospital Cards */}
        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <Card key={i} className="rounded-xl">
                <CardContent className="p-6">
                  <Skeleton className="h-8 w-3/4 mb-4" />
                  <Skeleton className="h-4 w-1/2 mb-2" />
                  <Skeleton className="h-4 w-2/3 mb-4" />
                  <div className="flex gap-2">
                    <Skeleton className="h-6 w-16" />
                    <Skeleton className="h-6 w-16" />
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        ) : filteredHospitals.length === 0 ? (
          <div className="text-center py-16">
            <Hospital className="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p className="text-gray-500 text-lg">{tr.noResults}</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredHospitals.map((hospital) => (
              <Card
                key={hospital.id}
                className="group rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-0 hover:-translate-y-1"
              >
                <CardContent className="p-6">
                  <div className="flex items-start gap-3 mb-4">
                    <div className="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                      <Hospital className="w-6 h-6 text-emerald-600" />
                    </div>
                    <div className="min-w-0">
                      <h3 className="font-bold text-gray-900 text-lg truncate">{isAr ? hospital.name : hospital.nameEn}</h3>
                      <p className="text-sm text-gray-500 flex items-center gap-1 mt-0.5">
                        <MapPin className="w-3.5 h-3.5 flex-shrink-0" />
                        <span className="truncate">{isAr ? hospital.location : hospital.locationEn}</span>
                      </p>
                    </div>
                  </div>

                  {/* Rating */}
                  <div className="flex items-center gap-1 mb-3">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-4 h-4 ${i < Math.floor(hospital.rating) ? 'text-amber-400 fill-amber-400' : 'text-gray-300'}`}
                      />
                    ))}
                    <span className="text-sm text-gray-600 ms-1">{hospital.rating}</span>
                  </div>

                  {/* Info */}
                  <div className="flex items-center gap-4 text-sm text-gray-500 mb-4">
                    <span className="flex items-center gap-1">
                      <Shield className="w-4 h-4 text-emerald-500" />
                      {hospital.departments} {tr.departments}
                    </span>
                    <span className="flex items-center gap-1">
                      <Users className="w-4 h-4 text-emerald-500" />
                      {hospital.doctors} {tr.doctors}
                    </span>
                  </div>

                  {/* Contact */}
                  {hospital.phone && (
                    <p className="text-sm text-gray-500 flex items-center gap-1 mb-2">
                      <Phone className="w-3.5 h-3.5" />
                      {hospital.phone}
                    </p>
                  )}

                  <Button
                    variant="outline"
                    className="w-full border-emerald-200 text-emerald-700 hover:bg-emerald-50"
                    onClick={() => navigateTo('hospital-detail', hospital.id)}
                  >
                    {tr.viewDetails}
                    <ChevronLeft className={`w-4 h-4 ms-1 ${!isAr ? 'rotate-180' : ''}`} />
                  </Button>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )

  // ═══════════════════════════════════════════════════════════
  // HOSPITAL DETAIL PAGE
  // ═══════════════════════════════════════════════════════════

  const renderHospitalDetail = () => {
    if (!selectedHospital) return null
    const hospital = selectedHospital

    return (
      <div className="min-h-screen bg-gray-50">
        {/* Header */}
        <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Button
              variant="ghost"
              className="text-white hover:bg-white/10 mb-4"
              onClick={() => navigateTo('hospitals')}
            >
              <ArrowLeft className={`w-4 h-4 me-1 ${!isAr ? 'rotate-180' : ''}`} />
              {tr.back}
            </Button>
            <h1 className="text-3xl font-bold mb-2">{isAr ? hospital.name : hospital.nameEn}</h1>
            <p className="text-emerald-100 flex items-center gap-2">
              <MapPin className="w-4 h-4" />
              {isAr ? hospital.location : hospital.locationEn}
            </p>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Hospital Info */}
            <Card className="lg:col-span-1 rounded-xl shadow-md border-0">
              <CardHeader>
                <CardTitle className="text-lg">{tr.hospitalInfo}</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {/* Rating */}
                <div className="flex items-center gap-1">
                  {[...Array(5)].map((_, i) => (
                    <Star
                      key={i}
                      className={`w-5 h-5 ${i < Math.floor(hospital.rating) ? 'text-amber-400 fill-amber-400' : 'text-gray-300'}`}
                    />
                  ))}
                  <span className="ms-2 font-medium">{hospital.rating}/5.0</span>
                </div>

                <Separator />

                {/* Stats */}
                <div className="grid grid-cols-2 gap-4">
                  <div className="text-center p-3 bg-emerald-50 rounded-lg">
                    <Shield className="w-5 h-5 text-emerald-600 mx-auto mb-1" />
                    <p className="text-lg font-bold text-gray-900">{hospital.departments}</p>
                    <p className="text-xs text-gray-500">{tr.departments}</p>
                  </div>
                  <div className="text-center p-3 bg-teal-50 rounded-lg">
                    <Users className="w-5 h-5 text-teal-600 mx-auto mb-1" />
                    <p className="text-lg font-bold text-gray-900">{hospital.doctors}</p>
                    <p className="text-xs text-gray-500">{tr.doctors}</p>
                  </div>
                </div>

                <Separator />

                {/* Contact */}
                <div className="space-y-3">
                  <h4 className="font-medium text-gray-900">{tr.contact}</h4>
                  {hospital.phone && (
                    <p className="flex items-center gap-2 text-sm text-gray-600">
                      <Phone className="w-4 h-4 text-emerald-500" />
                      {hospital.phone}
                    </p>
                  )}
                  {hospital.email && (
                    <p className="flex items-center gap-2 text-sm text-gray-600">
                      <Mail className="w-4 h-4 text-emerald-500" />
                      {hospital.email}
                    </p>
                  )}
                </div>
              </CardContent>
            </Card>

            {/* Departments & Doctors */}
            <div className="lg:col-span-2 space-y-6">
              {/* Departments */}
              <Card className="rounded-xl shadow-md border-0">
                <CardHeader>
                  <CardTitle className="text-lg">{tr.departments}</CardTitle>
                </CardHeader>
                <CardContent>
                  {hospital.departmentsList && hospital.departmentsList.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      {hospital.departmentsList.map((dept) => (
                        <div
                          key={dept.id}
                          className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-emerald-50 transition-colors cursor-pointer"
                          onClick={() => {
                            setFilterDepartmentId(dept.id)
                            navigateTo('doctors')
                          }}
                        >
                          <div className="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                            <Shield className="w-5 h-5 text-emerald-600" />
                          </div>
                          <div>
                            <p className="font-medium text-gray-900 text-sm">{isAr ? dept.name : dept.nameEn}</p>
                            <p className="text-xs text-gray-500">{tr.department}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-gray-500 text-center py-4">{tr.noResults}</p>
                  )}
                </CardContent>
              </Card>

              {/* Doctors in Hospital */}
              <Card className="rounded-xl shadow-md border-0">
                <CardHeader>
                  <CardTitle className="text-lg">{tr.doctorsInHospital}</CardTitle>
                </CardHeader>
                <CardContent>
                  {hospital.doctorsList && hospital.doctorsList.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      {hospital.doctorsList.map((doc) => (
                        <div
                          key={doc.id}
                          className="p-4 border border-gray-100 rounded-xl hover:border-emerald-200 hover:shadow-sm transition-all cursor-pointer"
                          onClick={() => navigateTo('doctor-detail', undefined, doc.id)}
                        >
                          <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                              <Stethoscope className="w-5 h-5 text-teal-600" />
                            </div>
                            <div className="min-w-0">
                              <p className="font-semibold text-gray-900 text-sm truncate">{isAr ? doc.name : doc.nameEn}</p>
                              <p className="text-xs text-emerald-600">{isAr ? doc.specialty : doc.specialtyEn}</p>
                            </div>
                          </div>
                          <div className="flex items-center justify-between mt-3">
                            <div className="flex items-center gap-1">
                              <Star className="w-3.5 h-3.5 text-amber-400 fill-amber-400" />
                              <span className="text-xs text-gray-600">{doc.rating}</span>
                            </div>
                            <span className="text-sm font-bold text-emerald-700">{doc.price} {tr.sar}</span>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-gray-500 text-center py-4">{tr.noResults}</p>
                  )}
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    )
  }

  // ═══════════════════════════════════════════════════════════
  // DOCTORS PAGE
  // ═══════════════════════════════════════════════════════════

  const renderDoctorsPage = () => (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold mb-2">{tr.doctorsTitle}</h1>
          <p className="text-emerald-100">{tr.doctorsSubtitle}</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Filters */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
          {/* Search */}
          <div className="relative">
            <Search className={`absolute top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 ${isAr ? 'right-3' : 'left-3'}`} />
            <Input
              placeholder={tr.searchDoctors}
              value={doctorSearch}
              onChange={(e) => setDoctorSearch(e.target.value)}
              className={`${isAr ? 'pr-10' : 'pl-10'} rounded-lg border-gray-200`}
            />
          </div>

          {/* Hospital Filter */}
          <Select value={filterHospitalId} onValueChange={setFilterHospitalId}>
            <SelectTrigger className="rounded-lg border-gray-200">
              <SelectValue placeholder={tr.filterByHospital} />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">{tr.allHospitals}</SelectItem>
              {hospitals.map((h) => (
                <SelectItem key={h.id} value={h.id}>
                  {isAr ? h.name : h.nameEn}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>

          {/* Department Filter */}
          <Select value={filterDepartmentId} onValueChange={setFilterDepartmentId}>
            <SelectTrigger className="rounded-lg border-gray-200">
              <SelectValue placeholder={tr.filterByDept} />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">{tr.allDepartments}</SelectItem>
              {allDepartments.map((d) => (
                <SelectItem key={d.id} value={d.id}>
                  {isAr ? d.name : d.nameEn}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>

        {/* Doctors Grid */}
        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map((i) => (
              <Card key={i} className="rounded-xl">
                <CardContent className="p-6">
                  <Skeleton className="h-8 w-3/4 mb-4" />
                  <Skeleton className="h-4 w-1/2 mb-2" />
                  <Skeleton className="h-4 w-2/3 mb-4" />
                </CardContent>
              </Card>
            ))}
          </div>
        ) : filteredDoctors.length === 0 ? (
          <div className="text-center py-16">
            <Stethoscope className="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p className="text-gray-500 text-lg">{tr.noResults}</p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredDoctors.map((doctor) => (
              <Card
                key={doctor.id}
                className="group rounded-xl shadow-md hover:shadow-lg transition-all duration-300 border-0 hover:-translate-y-1"
              >
                <CardContent className="p-6">
                  <div className="flex items-start gap-3 mb-4">
                    <div className="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                      <Stethoscope className="w-6 h-6 text-teal-600" />
                    </div>
                    <div className="min-w-0">
                      <h3 className="font-bold text-gray-900 text-lg truncate">{isAr ? doctor.name : doctor.nameEn}</h3>
                      <p className="text-sm text-emerald-600 font-medium">{isAr ? doctor.specialty : doctor.specialtyEn}</p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3 mb-3">
                    <div className="flex items-center gap-1">
                      <Star className="w-4 h-4 text-amber-400 fill-amber-400" />
                      <span className="text-sm font-medium text-gray-700">{doctor.rating}</span>
                    </div>
                    <span className="text-sm text-gray-500">
                      <BriefcaseMedical className="w-3.5 h-3.5 inline me-1" />
                      {doctor.experience} {tr.experience}
                    </span>
                  </div>

                  <p className="text-sm text-gray-500 flex items-center gap-1 mb-4">
                    <Hospital className="w-3.5 h-3.5" />
                    {isAr ? doctor.hospital.name : doctor.hospital.nameEn}
                  </p>

                  <div className="flex items-center justify-between mb-4">
                    <span className="text-lg font-bold text-emerald-700">
                      {doctor.price} <span className="text-sm font-normal text-gray-500">{tr.sar}</span>
                    </span>
                  </div>

                  <div className="flex gap-2">
                    <Button
                      className="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white"
                      onClick={() => navigateToBooking(doctor.id, doctor.hospitalId)}
                    >
                      {tr.bookNow}
                    </Button>
                    <Button
                      variant="outline"
                      className="border-emerald-200 text-emerald-700 hover:bg-emerald-50"
                      onClick={() => navigateTo('doctor-detail', undefined, doctor.id)}
                    >
                      ...
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )

  // ═══════════════════════════════════════════════════════════
  // DOCTOR DETAIL PAGE
  // ═══════════════════════════════════════════════════════════

  const renderDoctorDetail = () => {
    if (!selectedDoctor) return null
    const doctor = selectedDoctor

    return (
      <div className="min-h-screen bg-gray-50">
        {/* Header */}
        <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <Button
              variant="ghost"
              className="text-white hover:bg-white/10 mb-4"
              onClick={() => navigateTo('doctors')}
            >
              <ArrowLeft className={`w-4 h-4 me-1 ${!isAr ? 'rotate-180' : ''}`} />
              {tr.back}
            </Button>
            <div className="flex flex-col sm:flex-row sm:items-center gap-4">
              <div className="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center">
                <Stethoscope className="w-8 h-8" />
              </div>
              <div>
                <h1 className="text-3xl font-bold">{isAr ? doctor.name : doctor.nameEn}</h1>
                <p className="text-emerald-100 text-lg">{isAr ? doctor.specialty : doctor.specialtyEn}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Doctor Info */}
            <div className="space-y-6">
              <Card className="rounded-xl shadow-md border-0">
                <CardHeader>
                  <CardTitle className="text-lg">{tr.doctorInfo}</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  {/* Rating */}
                  <div className="flex items-center gap-1">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-5 h-5 ${i < Math.floor(doctor.rating) ? 'text-amber-400 fill-amber-400' : 'text-gray-300'}`}
                      />
                    ))}
                    <span className="ms-2 font-medium">{doctor.rating}/5.0</span>
                  </div>

                  <Separator />

                  {/* Stats */}
                  <div className="grid grid-cols-2 gap-4">
                    <div className="text-center p-3 bg-emerald-50 rounded-lg">
                      <BriefcaseMedical className="w-5 h-5 text-emerald-600 mx-auto mb-1" />
                      <p className="text-lg font-bold text-gray-900">{doctor.experience}</p>
                      <p className="text-xs text-gray-500">{tr.experience}</p>
                    </div>
                    <div className="text-center p-3 bg-teal-50 rounded-lg">
                      <Award className="w-5 h-5 text-teal-600 mx-auto mb-1" />
                      <p className="text-lg font-bold text-gray-900">{doctor.price}</p>
                      <p className="text-xs text-gray-500">{tr.sar}</p>
                    </div>
                  </div>

                  <Separator />

                  {/* Hospital & Department */}
                  <div className="space-y-3">
                    <p className="flex items-center gap-2 text-sm text-gray-600">
                      <Hospital className="w-4 h-4 text-emerald-500" />
                      {isAr ? doctor.hospital.name : doctor.hospital.nameEn}
                    </p>
                    <p className="flex items-center gap-2 text-sm text-gray-600">
                      <Shield className="w-4 h-4 text-emerald-500" />
                      {isAr ? doctor.department.name : doctor.department.nameEn}
                    </p>
                  </div>
                </CardContent>
              </Card>

              {/* Book Button */}
              <Button
                className="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-6 text-lg"
                onClick={() => navigateToBooking(doctor.id, doctor.hospitalId)}
              >
                <Calendar className="w-5 h-5 me-2" />
                {tr.bookAppointment}
              </Button>
            </div>

            {/* Bio & Schedule */}
            <div className="lg:col-span-2 space-y-6">
              {/* Bio */}
              <Card className="rounded-xl shadow-md border-0">
                <CardHeader>
                  <CardTitle className="text-lg">{tr.bio}</CardTitle>
                </CardHeader>
                <CardContent>
                  <p className="text-gray-600 leading-relaxed">
                    {isAr ? doctor.bio : doctor.bioEn}
                  </p>
                </CardContent>
              </Card>

              {/* Schedule */}
              <Card className="rounded-xl shadow-md border-0">
                <CardHeader>
                  <CardTitle className="text-lg">{tr.weeklySchedule}</CardTitle>
                </CardHeader>
                <CardContent>
                  {doctor.schedules && doctor.schedules.length > 0 ? (
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                      {doctor.schedules.map((schedule) => (
                        <div
                          key={schedule.id}
                          className="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                        >
                          <div className="flex items-center gap-2">
                            <Clock className="w-4 h-4 text-emerald-500" />
                            <span className="font-medium text-gray-900 text-sm">
                              {getDayTranslation(schedule.day)}
                            </span>
                          </div>
                          <div className="flex items-center gap-1">
                            <Badge variant="outline" className="text-xs text-emerald-700 border-emerald-200">
                              {schedule.startTime} - {schedule.endTime}
                            </Badge>
                          </div>
                        </div>
                      ))}
                    </div>
                  ) : (
                    <p className="text-gray-500 text-center py-4">{tr.noResults}</p>
                  )}
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>
    )
  }

  // ═══════════════════════════════════════════════════════════
  // BOOKING PAGE
  // ═══════════════════════════════════════════════════════════

  const renderBookingPage = () => (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold mb-2">{tr.bookingTitle}</h1>
          <p className="text-emerald-100">{tr.heroSubtitle}</p>
        </div>
      </div>

      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {bookingSuccess ? (
          <Card className="rounded-xl shadow-md border-0">
            <CardContent className="p-8 text-center">
              <div className="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                <CheckCircle2 className="w-8 h-8 text-emerald-600" />
              </div>
              <h2 className="text-2xl font-bold text-gray-900 mb-2">{tr.bookingSuccess}</h2>
              <p className="text-gray-600 mb-6">{tr.bookingSuccessMsg}</p>
              <div className="flex flex-col sm:flex-row gap-3 justify-center">
                <Button
                  className="bg-emerald-600 hover:bg-emerald-700 text-white"
                  onClick={() => navigateToBooking()}
                >
                  {tr.bookAppointment}
                </Button>
                <Button
                  variant="outline"
                  onClick={() => navigateTo('home')}
                >
                  {tr.goToHome}
                </Button>
              </div>
            </CardContent>
          </Card>
        ) : (
          <Card className="rounded-xl shadow-md border-0">
            <CardContent className="p-6 sm:p-8">
              <form onSubmit={handleBooking} className="space-y-6">
                {/* Patient Name */}
                <div className="space-y-2">
                  <Label htmlFor="patientName">{tr.patientName} *</Label>
                  <Input
                    id="patientName"
                    value={bookingForm.patientName}
                    onChange={(e) => setBookingForm({ ...bookingForm, patientName: e.target.value })}
                    placeholder={tr.patientName}
                    required
                    className="rounded-lg"
                  />
                </div>

                {/* Phone */}
                <div className="space-y-2">
                  <Label htmlFor="patientPhone">{tr.patientPhone} *</Label>
                  <Input
                    id="patientPhone"
                    type="tel"
                    value={bookingForm.patientPhone}
                    onChange={(e) => setBookingForm({ ...bookingForm, patientPhone: e.target.value })}
                    placeholder="+966-5X-XXX-XXXX"
                    required
                    className="rounded-lg"
                  />
                </div>

                {/* Email */}
                <div className="space-y-2">
                  <Label htmlFor="patientEmail">{tr.patientEmail}</Label>
                  <Input
                    id="patientEmail"
                    type="email"
                    value={bookingForm.patientEmail}
                    onChange={(e) => setBookingForm({ ...bookingForm, patientEmail: e.target.value })}
                    placeholder={tr.email}
                    className="rounded-lg"
                  />
                </div>

                {/* Hospital Selection */}
                <div className="space-y-2">
                  <Label>{tr.selectHospital} *</Label>
                  <Select
                    value={bookingForm.hospitalId}
                    onValueChange={(val) => setBookingForm({ ...bookingForm, hospitalId: val, doctorId: '' })}
                  >
                    <SelectTrigger className="rounded-lg">
                      <SelectValue placeholder={tr.selectHospital} />
                    </SelectTrigger>
                    <SelectContent>
                      {hospitals.map((h) => (
                        <SelectItem key={h.id} value={h.id}>
                          {isAr ? h.name : h.nameEn}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                </div>

                {/* Doctor Selection */}
                <div className="space-y-2">
                  <Label>{tr.selectDoctor} *</Label>
                  <Select
                    value={bookingForm.doctorId}
                    onValueChange={(val) => {
                      const selectedDoc = doctors.find((d) => d.id === val)
                      setBookingForm({
                        ...bookingForm,
                        doctorId: val,
                        hospitalId: selectedDoc ? selectedDoc.hospitalId : bookingForm.hospitalId,
                      })
                    }}
                  >
                    <SelectTrigger className="rounded-lg">
                      <SelectValue placeholder={tr.selectDoctor} />
                    </SelectTrigger>
                    <SelectContent>
                      {doctors
                        .filter((d) => !bookingForm.hospitalId || d.hospitalId === bookingForm.hospitalId)
                        .map((d) => (
                          <SelectItem key={d.id} value={d.id}>
                            {isAr ? d.name : d.nameEn} - {isAr ? d.specialty : d.specialtyEn} ({d.price} {tr.sar})
                          </SelectItem>
                        ))}
                    </SelectContent>
                  </Select>
                </div>

                {/* Date */}
                <div className="space-y-2">
                  <Label htmlFor="date">{tr.selectDate} *</Label>
                  <Input
                    id="date"
                    type="date"
                    value={bookingForm.date}
                    onChange={(e) => setBookingForm({ ...bookingForm, date: e.target.value })}
                    min={new Date().toISOString().split('T')[0]}
                    required
                    className="rounded-lg"
                  />
                </div>

                {/* Time Slots */}
                <div className="space-y-2">
                  <Label>{tr.selectTime} *</Label>
                  <div className="grid grid-cols-4 sm:grid-cols-6 gap-2">
                    {timeSlots.map((slot) => (
                      <button
                        key={slot}
                        type="button"
                        className={`py-2 px-3 text-sm rounded-lg border transition-all duration-200 ${
                          bookingForm.time === slot
                            ? 'bg-emerald-600 text-white border-emerald-600'
                            : 'bg-white text-gray-700 border-gray-200 hover:border-emerald-400 hover:text-emerald-700'
                        }`}
                        onClick={() => setBookingForm({ ...bookingForm, time: slot })}
                      >
                        {slot}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Notes */}
                <div className="space-y-2">
                  <Label htmlFor="notes">{tr.notes}</Label>
                  <Input
                    id="notes"
                    value={bookingForm.notes}
                    onChange={(e) => setBookingForm({ ...bookingForm, notes: e.target.value })}
                    placeholder={tr.notes}
                    className="rounded-lg"
                  />
                </div>

                {/* Submit */}
                <Button
                  type="submit"
                  disabled={bookingLoading}
                  className="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-6 text-lg rounded-lg"
                >
                  {bookingLoading ? (
                    <div className="flex items-center gap-2">
                      <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      {isAr ? 'جاري الحجز...' : 'Booking...'}
                    </div>
                  ) : (
                    <>
                      <CheckCircle2 className="w-5 h-5 me-2" />
                      {tr.submitBooking}
                    </>
                  )}
                </Button>
              </form>
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  )

  // ═══════════════════════════════════════════════════════════
  // MY APPOINTMENTS PAGE
  // ═══════════════════════════════════════════════════════════

  const renderMyAppointments = () => (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold mb-2">{tr.myAppointments}</h1>
          <p className="text-emerald-100">{tr.appointmentDetails}</p>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Status Filter */}
        <div className="mb-6">
          <Select value={appointmentStatusFilter} onValueChange={setAppointmentStatusFilter}>
            <SelectTrigger className="w-full sm:w-64 rounded-lg border-gray-200">
              <SelectValue placeholder={tr.filterByStatus} />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">{tr.allStatuses}</SelectItem>
              <SelectItem value="pending">{tr.pending}</SelectItem>
              <SelectItem value="confirmed">{tr.confirmed}</SelectItem>
              <SelectItem value="cancelled">{tr.cancelled}</SelectItem>
              <SelectItem value="completed">{tr.completed}</SelectItem>
            </SelectContent>
          </Select>
        </div>

        {/* Appointments List */}
        {loading ? (
          <div className="space-y-4">
            {[1, 2, 3].map((i) => (
              <Card key={i} className="rounded-xl">
                <CardContent className="p-6">
                  <Skeleton className="h-6 w-3/4 mb-3" />
                  <Skeleton className="h-4 w-1/2 mb-2" />
                  <Skeleton className="h-4 w-2/3" />
                </CardContent>
              </Card>
            ))}
          </div>
        ) : filteredAppointments.length === 0 ? (
          <div className="text-center py-16">
            <Calendar className="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <p className="text-gray-500 text-lg">{tr.noResults}</p>
          </div>
        ) : (
          <div className="space-y-4">
            {filteredAppointments.map((appointment) => (
              <Card
                key={appointment.id}
                className="rounded-xl shadow-md border-0 hover:shadow-lg transition-all duration-300"
              >
                <CardContent className="p-6">
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div className="space-y-2">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                          <Stethoscope className="w-5 h-5 text-teal-600" />
                        </div>
                        <div>
                          <h3 className="font-semibold text-gray-900">
                            {isAr ? appointment.doctor.name : appointment.doctor.nameEn}
                          </h3>
                          <p className="text-sm text-emerald-600">{isAr ? appointment.doctor.specialty : appointment.doctor.specialtyEn}</p>
                        </div>
                      </div>

                      <div className="flex flex-wrap items-center gap-3 text-sm text-gray-500 ms-13">
                        <span className="flex items-center gap-1">
                          <Calendar className="w-3.5 h-3.5" />
                          {appointment.date}
                        </span>
                        <span className="flex items-center gap-1">
                          <Clock className="w-3.5 h-3.5" />
                          {appointment.time}
                        </span>
                        <span className="flex items-center gap-1">
                          <Hospital className="w-3.5 h-3.5" />
                          {isAr ? appointment.doctor.hospital.name : appointment.doctor.hospital.nameEn}
                        </span>
                      </div>
                    </div>

                    <div className="flex items-center gap-3">
                      <Badge className={`text-xs ${getStatusColor(appointment.status)}`}>
                        {getStatusText(appointment.status)}
                      </Badge>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        )}
      </div>
    </div>
  )

  // ═══════════════════════════════════════════════════════════
  // LOGIN PAGE
  // ═══════════════════════════════════════════════════════════

  const renderLoginPage = () => (
    <div className="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
      <Card className="w-full max-w-md rounded-xl shadow-lg border-0">
        <CardContent className="p-8">
          {/* Logo */}
          <div className="text-center mb-8">
            <div className="w-14 h-14 bg-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-4">
              <Activity className="w-7 h-7 text-white" />
            </div>
            <h1 className="text-2xl font-bold text-gray-900">{tr.loginTitle}</h1>
            <p className="text-gray-500 mt-1">{tr.loginSubtitle}</p>
          </div>

          <form onSubmit={handleLogin} className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="loginEmail">{tr.email}</Label>
              <Input
                id="loginEmail"
                type="email"
                value={loginForm.email}
                onChange={(e) => setLoginForm({ ...loginForm, email: e.target.value })}
                placeholder={tr.email}
                required
                className="rounded-lg"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="loginPassword">{tr.password}</Label>
              <Input
                id="loginPassword"
                type="password"
                value={loginForm.password}
                onChange={(e) => setLoginForm({ ...loginForm, password: e.target.value })}
                placeholder={tr.password}
                required
                className="rounded-lg"
              />
            </div>

            <Button
              type="submit"
              className="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-5 rounded-lg"
            >
              <LogIn className="w-4 h-4 me-2" />
              {tr.loginBtn}
            </Button>
          </form>

          <div className="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
            <p className="text-sm text-amber-800">
              <strong>{isAr ? 'ملاحظة:' : 'Note:'}</strong> {tr.demoNote}
            </p>
          </div>

          <Separator className="my-6" />

          <div className="text-center">
            <p className="text-sm text-gray-500 mb-3">{tr.adminNote}</p>
            <Button
              variant="outline"
              className="border-emerald-200 text-emerald-700 hover:bg-emerald-50"
              onClick={() => { setIsAdmin(true); setPage('admin'); fetchAdminStats(); }}
            >
              <LayoutDashboard className="w-3.5 h-3.5 me-1" />
              {isAr ? 'الدخول للوحة التحكم' : 'Open Admin Dashboard'}
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  )

  // ═══════════════════════════════════════════════════════════
  // HOME PAGE (Full)
  // ═══════════════════════════════════════════════════════════

  const renderHomePage = () => (
    <>
      {renderHero()}
      {renderServices()}
      {renderHospitalsSection()}
      {renderDoctorsSection()}
      {renderTestimonials()}
    </>
  )

  // ═══════════════════════════════════════════════════════════
  // ADMIN DASHBOARD
  // ═══════════════════════════════════════════════════════════

  const fetchAdminStats = async () => {
    try {
      const [hospitalsRes, doctorsRes, apptsRes] = await Promise.all([
        fetch('/api/hospitals'),
        fetch('/api/doctors'),
        fetch('/api/appointments'),
      ])
      const hosp = await hospitalsRes.json()
      const docs = await doctorsRes.json()
      const appts = await apptsRes.json()
      // Also update the main state for use in admin tabs
      if (hosp.length > 0 && hospitals.length === 0) setHospitals(hosp)
      if (docs.length > 0 && doctors.length === 0) setDoctors(docs)
      setAppointments(appts)
      setAdminStats({
        totalHospitals: hosp.length,
        totalDoctors: docs.length,
        totalAppointments: appts.length,
        todayAppointments: appts.filter((a: AppointmentData) => a.date === new Date().toISOString().split('T')[0]).length,
        pendingAppointments: appts.filter((a: AppointmentData) => a.status === 'pending').length,
        confirmedAppointments: appts.filter((a: AppointmentData) => a.status === 'confirmed').length,
        completedAppointments: appts.filter((a: AppointmentData) => a.status === 'completed').length,
        cancelledAppointments: appts.filter((a: AppointmentData) => a.status === 'cancelled').length,
        totalRevenue: docs.reduce((sum: number, d: DoctorData) => sum + d.price, 0),
      })
    } catch (e) { console.error(e) }
  }

  const handleUpdateAppointmentStatus = async (id: string, status: string) => {
    try {
      await fetch(`/api/appointments?id=${id}&status=${status}`, { method: 'PUT' })
      const apptsRes = await fetch('/api/appointments')
      const appts = await apptsRes.json()
      setAppointments(appts)
      fetchAdminStats()
    } catch (e) { console.error(e) }
  }

  const handleDeleteAppointment = async (id: string) => {
    try {
      await fetch(`/api/appointments?id=${id}`, { method: 'DELETE' })
      setDeleteConfirm(null)
      const apptsRes = await fetch('/api/appointments')
      const appts = await apptsRes.json()
      setAppointments(appts)
      fetchAdminStats()
    } catch (e) { console.error(e) }
  }

  const renderAdminDashboard = () => {
    const t = lang === 'ar'
      ? { overview: 'نظرة عامة', hospitals: 'المستشفيات', doctors: 'الأطباء', appointments: 'المواعيد', departments: 'الأقسام', back: 'العودة للموقع', totalHospitals: 'إجمالي المستشفيات', totalDoctors: 'إجمالي الأطباء', totalAppointments: 'إجمالي المواعيد', todayAppointments: 'مواعيد اليوم', pendingAppointments: 'مواعيد معلقة', confirmedAppointments: 'مواعيد مؤكدة', completedAppointments: 'مواعيد مكتملة', cancelledAppointments: 'مواعيد ملغاة', totalRevenue: 'إجمالي الإيرادات', recentAppointments: 'أحدث المواعيد', patientName: 'اسم المريض', doctor: 'الطبيب', date: 'التاريخ', time: 'الوقت', status: 'الحالة', actions: 'الإجراءات', noData: 'لا توجد بيانات', currency: 'ر.س' }
      : { overview: 'Overview', hospitals: 'Hospitals', doctors: 'Doctors', appointments: 'Appointments', departments: 'Departments', back: 'Back to Website', totalHospitals: 'Total Hospitals', totalDoctors: 'Total Doctors', totalAppointments: 'Total Appointments', todayAppointments: "Today's Appointments", pendingAppointments: 'Pending', confirmedAppointments: 'Confirmed', completedAppointments: 'Completed', cancelledAppointments: 'Cancelled', totalRevenue: 'Total Revenue', recentAppointments: 'Recent Appointments', patientName: 'Patient', doctor: 'Doctor', date: 'Date', time: 'Time', status: 'Status', actions: 'Actions', noData: 'No data', currency: 'SAR' }

    const statusColors: Record<string, string> = {
      pending: 'bg-yellow-100 text-yellow-800',
      confirmed: 'bg-blue-100 text-blue-800',
      completed: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800',
    }
    const statusNames: Record<string, string> = lang === 'ar'
      ? { pending: 'معلق', confirmed: 'مؤكد', completed: 'مكتمل', cancelled: 'ملغي' }
      : { pending: 'Pending', confirmed: 'Confirmed', completed: 'Completed', cancelled: 'Cancelled' }

    const statCards = [
      { label: t.totalHospitals, value: adminStats?.totalHospitals || 0, icon: <Hospital className="w-6 h-6" />, color: 'from-emerald-500 to-emerald-600' },
      { label: t.totalDoctors, value: adminStats?.totalDoctors || 0, icon: <UserCog className="w-6 h-6" />, color: 'from-teal-500 to-teal-600' },
      { label: t.totalAppointments, value: adminStats?.totalAppointments || 0, icon: <ClipboardList className="w-6 h-6" />, color: 'from-blue-500 to-blue-600' },
      { label: t.totalRevenue, value: `${(adminStats?.totalRevenue || 0).toLocaleString()} ${t.currency}`, icon: <DollarSign className="w-6 h-6" />, color: 'from-amber-500 to-amber-600' },
    ]

    return (
      <div className="min-h-screen bg-gray-50">
        {/* Admin Top Bar */}
        <div className="bg-slate-900 text-white shadow-lg">
          <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <LayoutDashboard className="w-6 h-6 text-emerald-400" />
              <h1 className="text-lg font-bold">MediCare Pro - {lang === 'ar' ? 'لوحة التحكم' : 'Admin Dashboard'}</h1>
            </div>
            <div className="flex items-center gap-2">
              <Button variant="ghost" size="sm" className="text-white hover:bg-slate-800" onClick={() => setLang(lang === 'ar' ? 'en' : 'ar')}>
                <Globe className="w-4 h-4 me-1" /> {lang === 'ar' ? 'EN' : 'عربي'}
              </Button>
              <Button variant="ghost" size="sm" className="text-emerald-400 hover:bg-slate-800" onClick={() => { setIsAdmin(false); setPage('home') }}>
                <ArrowLeft className="w-4 h-4 me-1" /> {t.back}
              </Button>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 py-6">
          {/* Admin Tabs */}
          <div className="flex flex-wrap gap-2 mb-6">
            {[
              { key: 'overview', label: t.overview, icon: <BarChart3 className="w-4 h-4" /> },
              { key: 'hospitals', label: t.hospitals, icon: <Hospital className="w-4 h-4" /> },
              { key: 'doctors', label: t.doctors, icon: <UserCog className="w-4 h-4" /> },
              { key: 'appointments', label: t.appointments, icon: <Calendar className="w-4 h-4" /> },
              { key: 'departments', label: t.departments, icon: <Settings className="w-4 h-4" /> },
            ].map((tab) => (
              <Button
                key={tab.key}
                variant={adminTab === tab.key ? 'default' : 'outline'}
                size="sm"
                className={adminTab === tab.key ? 'bg-emerald-600 hover:bg-emerald-700' : ''}
                onClick={() => setAdminTab(tab.key)}
              >
                {tab.icon} <span className="ms-1">{tab.label}</span>
              </Button>
            ))}
          </div>

          {/* Overview Tab */}
          {adminTab === 'overview' && (
            <div className="space-y-6">
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {statCards.map((card, i) => (
                  <Card key={i} className="overflow-hidden">
                    <div className={`bg-gradient-to-r ${card.color} p-4 text-white`}>
                      <div className="flex items-center justify-between">
                        <span className="text-sm opacity-90">{card.label}</span>
                        {card.icon}
                      </div>
                      <p className="text-2xl font-bold mt-2">{card.value}</p>
                    </div>
                  </Card>
                ))}
              </div>

              {/* Status breakdown */}
              <Card>
                <CardHeader><CardTitle>{lang === 'ar' ? 'تفاصيل المواعيد' : 'Appointment Breakdown'}</CardTitle></CardHeader>
                <CardContent>
                  <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {[
                      { label: t.todayAppointments, value: adminStats?.todayAppointments || 0, color: 'text-blue-600' },
                      { label: t.pendingAppointments, value: adminStats?.pendingAppointments || 0, color: 'text-yellow-600' },
                      { label: t.confirmedAppointments, value: adminStats?.confirmedAppointments || 0, color: 'text-green-600' },
                      { label: t.cancelledAppointments, value: adminStats?.cancelledAppointments || 0, color: 'text-red-600' },
                    ].map((item, i) => (
                      <div key={i} className="text-center p-4 bg-gray-50 rounded-lg">
                        <p className={`text-3xl font-bold ${item.color}`}>{item.value}</p>
                        <p className="text-sm text-gray-600 mt-1">{item.label}</p>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>

              {/* Recent Appointments Table */}
              <Card>
                <CardHeader><CardTitle>{t.recentAppointments}</CardTitle></CardHeader>
                <CardContent>
                  <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b bg-gray-50">
                          <th className={`p-3 text-start ${lang === 'ar' ? 'text-right' : ''}`}>{t.patientName}</th>
                          <th className={`p-3 text-start ${lang === 'ar' ? 'text-right' : ''}`}>{t.doctor}</th>
                          <th className={`p-3 text-start ${lang === 'ar' ? 'text-right' : ''}`}>{t.date}</th>
                          <th className={`p-3 text-start ${lang === 'ar' ? 'text-right' : ''}`}>{t.time}</th>
                          <th className={`p-3 text-start ${lang === 'ar' ? 'text-right' : ''}`}>{t.status}</th>
                        </tr>
                      </thead>
                      <tbody>
                        {appointments.slice(0, 8).map((appt) => (
                          <tr key={appt.id} className="border-b hover:bg-gray-50">
                            <td className="p-3 font-medium">{appt.patientName}</td>
                            <td className="p-3">{appt.doctor?.name || '-'}</td>
                            <td className="p-3">{appt.date}</td>
                            <td className="p-3">{appt.time}</td>
                            <td className="p-3"><Badge className={statusColors[appt.status] || ''}>{statusNames[appt.status] || appt.status}</Badge></td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </CardContent>
              </Card>
            </div>
          )}

          {/* Hospitals Tab */}
          {adminTab === 'hospitals' && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              {hospitals.map((h) => (
                <Card key={h.id} className="hover:shadow-lg transition-shadow">
                  <CardHeader>
                    <div className="flex items-start justify-between">
                      <div>
                        <CardTitle className="text-lg">{lang === 'ar' ? h.name : h.nameEn}</CardTitle>
                        <CardDescription className="flex items-center gap-1 mt-1">
                          <MapPin className="w-3.5 h-3.5" /> {lang === 'ar' ? h.location : h.locationEn}
                        </CardDescription>
                      </div>
                      <div className="flex items-center gap-1 bg-emerald-50 text-emerald-700 px-2 py-1 rounded-md">
                        <Star className="w-3.5 h-3.5 fill-current" />
                        <span className="text-sm font-bold">{h.rating}</span>
                      </div>
                    </div>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-2 text-sm text-gray-600">
                      <div className="flex items-center gap-2"><Phone className="w-4 h-4" /> {h.phone || '-'}</div>
                      <div className="flex items-center gap-2"><Mail className="w-4 h-4" /> {h.email || '-'}</div>
                      <div className="flex gap-4 pt-2">
                        <Badge variant="secondary"><Building2 className="w-3 h-3 me-1" /> {h.departmentsList?.length || h.departments || 0} {lang === 'ar' ? 'أقسام' : 'Dept.'}</Badge>
                        <Badge variant="secondary"><Users className="w-3 h-3 me-1" /> {h.doctorsList?.length || h.doctors || 0} {lang === 'ar' ? 'أطباء' : 'Doctors'}</Badge>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          )}

          {/* Doctors Tab */}
          {adminTab === 'doctors' && (
            <div className="overflow-x-auto">
              <Card>
                <CardContent className="p-0">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b bg-gray-50">
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'الاسم' : 'Name'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'التخصص' : 'Specialty'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'المستشفى' : 'Hospital'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'التقييم' : 'Rating'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'الخبرة' : 'Exp.'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'السعر' : 'Price'}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{lang === 'ar' ? 'الحالة' : 'Status'}</th>
                      </tr>
                    </thead>
                    <tbody>
                      {doctors.map((d) => (
                        <tr key={d.id} className="border-b hover:bg-gray-50">
                          <td className="p-3 font-medium">{lang === 'ar' ? d.name : d.nameEn}</td>
                          <td className="p-3">{lang === 'ar' ? d.specialty : d.specialtyEn}</td>
                          <td className="p-3">{lang === 'ar' ? d.hospital?.name : d.hospital?.nameEn}</td>
                          <td className="p-3"><div className="flex items-center gap-1"><Star className="w-3.5 h-3.5 fill-current text-amber-500" /> {d.rating}</div></td>
                          <td className="p-3">{d.experience} {lang === 'ar' ? 'سنة' : 'yrs'}</td>
                          <td className="p-3">{d.price} {t.currency}</td>
                          <td className="p-3"><Badge className={d.available ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}>{d.available ? (lang === 'ar' ? 'متاح' : 'Available') : (lang === 'ar' ? 'غير متاح' : 'Unavailable')}</Badge></td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </CardContent>
              </Card>
            </div>
          )}

          {/* Appointments Tab */}
          {adminTab === 'appointments' && (
            <div className="overflow-x-auto">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                  <CardTitle>{lang === 'ar' ? 'إدارة المواعيد' : 'Manage Appointments'}</CardTitle>
                  <div className="flex gap-2">
                    {[{ key: 'all', label: lang === 'ar' ? 'الكل' : 'All' }, { key: 'pending', label: statusNames.pending }, { key: 'confirmed', label: statusNames.confirmed }, { key: 'completed', label: statusNames.completed }, { key: 'cancelled', label: statusNames.cancelled }].map((filter) => (
                      <Button key={filter.key} variant="outline" size="sm" className="text-xs">{filter.label}</Button>
                    ))}
                  </div>
                </CardHeader>
                <CardContent className="p-0">
                  <table className="w-full text-sm">
                    <thead>
                      <tr className="border-b bg-gray-50">
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.patientName}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.doctor}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.date}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.time}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.status}</th>
                        <th className={`p-3 text-start font-semibold ${lang === 'ar' ? 'text-right' : ''}`}>{t.actions}</th>
                      </tr>
                    </thead>
                    <tbody>
                      {appointments.map((appt) => (
                        <tr key={appt.id} className="border-b hover:bg-gray-50">
                          <td className="p-3 font-medium">{appt.patientName}</td>
                          <td className="p-3">{appt.doctor?.name || '-'}</td>
                          <td className="p-3">{appt.date}</td>
                          <td className="p-3">{appt.time}</td>
                          <td className="p-3"><Badge className={statusColors[appt.status] || ''}>{statusNames[appt.status] || appt.status}</Badge></td>
                          <td className="p-3">
                            <div className="flex gap-1 flex-wrap">
                              {appt.status === 'pending' && (
                                <Button size="sm" className="h-7 text-xs bg-blue-600 hover:bg-blue-700" onClick={() => handleUpdateAppointmentStatus(appt.id, 'confirmed')}>
                                  <CheckCircle2 className="w-3 h-3 me-1" /> {lang === 'ar' ? 'تأكيد' : 'Confirm'}
                                </Button>
                              )}
                              {appt.status === 'confirmed' && (
                                <Button size="sm" className="h-7 text-xs bg-green-600 hover:bg-green-700" onClick={() => handleUpdateAppointmentStatus(appt.id, 'completed')}>
                                  <CheckCircle2 className="w-3 h-3 me-1" /> {lang === 'ar' ? 'إكمال' : 'Complete'}
                                </Button>
                              )}
                              {appt.status !== 'cancelled' && appt.status !== 'completed' && (
                                <Button size="sm" variant="destructive" className="h-7 text-xs" onClick={() => handleUpdateAppointmentStatus(appt.id, 'cancelled')}>
                                  <X className="w-3 h-3 me-1" /> {lang === 'ar' ? 'إلغاء' : 'Cancel'}
                                </Button>
                              )}
                              <Button size="sm" variant="outline" className="h-7 text-xs text-red-600" onClick={() => setDeleteConfirm(appt.id)}>
                                <Trash2 className="w-3 h-3" />
                              </Button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </CardContent>
              </Card>

              {/* Delete Confirmation Dialog */}
              <Dialog open={!!deleteConfirm} onOpenChange={() => setDeleteConfirm(null)}>
                <DialogContent>
                  <DialogHeader>
                    <DialogTitle>{lang === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete'}</DialogTitle>
                    <DialogDescription>{lang === 'ar' ? 'هل أنت متأكد من حذف هذا الموعد؟' : 'Are you sure you want to delete this appointment?'}</DialogDescription>
                  </DialogHeader>
                  <div className="flex gap-2 justify-end mt-4">
                    <Button variant="outline" onClick={() => setDeleteConfirm(null)}>{lang === 'ar' ? 'إلغاء' : 'Cancel'}</Button>
                    <Button variant="destructive" onClick={() => deleteConfirm && handleDeleteAppointment(deleteConfirm)}>{lang === 'ar' ? 'حذف' : 'Delete'}</Button>
                  </div>
                </DialogContent>
              </Dialog>
            </div>
          )}

          {/* Departments Tab */}
          {adminTab === 'departments' && (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
              {Array.from(new Set(doctors.map(d => d.department?.name))).map((dept, i) => {
                const deptDocs = doctors.filter(d => d.department?.name === dept)
                return (
                  <Card key={i} className="hover:shadow-lg transition-shadow">
                    <CardContent className="p-4">
                      <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                          <Stethoscope className="w-5 h-5 text-emerald-600" />
                        </div>
                        <div>
                          <p className="font-semibold">{dept}</p>
                          <p className="text-sm text-gray-500">{deptDocs.length} {lang === 'ar' ? 'أطباء' : 'doctors'}</p>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                )
              })}
            </div>
          )}
        </div>
      </div>
    )
  }

  // ═══════════════════════════════════════════════════════════
  // MAIN RENDER
  // ═══════════════════════════════════════════════════════════

  return (
    <div className="min-h-screen flex flex-col bg-white">
      {renderNavbar()}

      <main className="flex-1">
        {page === 'home' && renderHomePage()}
        {page === 'hospitals' && renderHospitalsPage()}
        {page === 'hospital-detail' && renderHospitalDetail()}
        {page === 'doctors' && renderDoctorsPage()}
        {page === 'doctor-detail' && renderDoctorDetail()}
        {page === 'booking' && renderBookingPage()}
        {page === 'my-appointments' && renderMyAppointments()}
        {page === 'login' && renderLoginPage()}
      {page === 'admin' && isAdmin && renderAdminDashboard()}
      </main>

      {/* Footer - show on home, hospitals, doctors, and detail pages */}
      {!['login', 'booking'].includes(page) && renderFooter()}
    </div>
  )
}
