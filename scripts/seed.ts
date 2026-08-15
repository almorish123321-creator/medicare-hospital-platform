import { PrismaClient } from '@prisma/client'

const prisma = new PrismaClient()

async function main() {
  // Create Hospitals
  const hospital1 = await prisma.hospital.upsert({
    where: { id: 'hosp-1' },
    update: {},
    create: {
      id: 'hosp-1',
      name: 'مستشفى ميدي كير المركزي',
      nameEn: 'MediCare Central Hospital',
      location: 'الرياض، حي العليا',
      locationEn: 'Riyadh, Olaya District',
      phone: '+966-11-234-5678',
      email: 'central@medicare.sa',
      rating: 4.8,
      departments: 8,
      doctors: 42,
    },
  })

  const hospital2 = await prisma.hospital.upsert({
    where: { id: 'hosp-2' },
    update: {},
    create: {
      id: 'hosp-2',
      name: 'مستشفى ميدي كير الشمال',
      nameEn: 'MediCare North Hospital',
      location: 'الرياض، حي الملقا',
      locationEn: 'Riyadh, Malqa District',
      phone: '+966-11-345-6789',
      email: 'north@medicare.sa',
      rating: 4.6,
      departments: 6,
      doctors: 28,
    },
  })

  const hospital3 = await prisma.hospital.upsert({
    where: { id: 'hosp-3' },
    update: {},
    create: {
      id: 'hosp-3',
      name: 'عيادة ميدي كير - جدة',
      nameEn: 'MediCare Jeddah Clinic',
      location: 'جدة، حي الروضة',
      locationEn: 'Jeddah, Rawdah District',
      phone: '+966-12-456-7890',
      email: 'jeddah@medicare.sa',
      rating: 4.7,
      departments: 5,
      doctors: 15,
    },
  })

  // Create Departments
  const deptData = [
    { id: 'dept-1', name: 'طب القلب والأوعية الدموية', nameEn: 'Cardiology', icon: 'Heart', hospitalId: hospital1.id },
    { id: 'dept-2', name: 'جراحة العظام', nameEn: 'Orthopedics', icon: 'Bone', hospitalId: hospital1.id },
    { id: 'dept-3', name: 'طب الأطفال', nameEn: 'Pediatrics', icon: 'Baby', hospitalId: hospital1.id },
    { id: 'dept-4', name: 'طب العيون', nameEn: 'Ophthalmology', icon: 'Eye', hospitalId: hospital1.id },
    { id: 'dept-5', name: 'طب الأسنان', nameEn: 'Dentistry', icon: 'Smile', hospitalId: hospital1.id },
    { id: 'dept-6', name: 'الأمراض الجلدية', nameEn: 'Dermatology', icon: 'Sparkles', hospitalId: hospital1.id },
    { id: 'dept-7', name: 'الطوارئ والحوادث', nameEn: 'Emergency', icon: 'Siren', hospitalId: hospital2.id },
    { id: 'dept-8', name: 'الأشعة التشخيصية', nameEn: 'Radiology', icon: 'Scan', hospitalId: hospital2.id },
    { id: 'dept-9', name: 'المختبر والتحاليل', nameEn: 'Laboratory', icon: 'Microscope', hospitalId: hospital2.id },
    { id: 'dept-10', name: 'الباطنة', nameEn: 'Internal Medicine', icon: 'Stethoscope', hospitalId: hospital3.id },
    { id: 'dept-11', name: 'النساء والتوليد', nameEn: 'Gynecology', icon: 'Users', hospitalId: hospital3.id },
    { id: 'dept-12', name: 'الصيدلة', nameEn: 'Pharmacy', icon: 'Pill', hospitalId: hospital3.id },
  ]

  for (const dept of deptData) {
    await prisma.department.upsert({
      where: { id: dept.id },
      update: {},
      create: dept,
    })
  }

  // Create Doctors
  const doctorData = [
    { id: 'doc-1', name: 'د. أحمد العتيبي', nameEn: 'Dr. Ahmed Al-Otaibi', specialty: 'طب القلب', specialtyEn: 'Cardiology', rating: 4.9, experience: 15, price: 500, hospitalId: hospital1.id, departmentId: 'dept-1' },
    { id: 'doc-2', name: 'د. سارة القحطاني', nameEn: 'Dr. Sara Al-Qahtani', specialty: 'جراحة العظام', specialtyEn: 'Orthopedics', rating: 4.7, experience: 12, price: 450, hospitalId: hospital1.id, departmentId: 'dept-2' },
    { id: 'doc-3', name: 'د. خالد المطيري', nameEn: 'Dr. Khalid Al-Mutairi', specialty: 'طب الأطفال', specialtyEn: 'Pediatrics', rating: 4.8, experience: 10, price: 350, hospitalId: hospital1.id, departmentId: 'dept-3' },
    { id: 'doc-4', name: 'د. نورة الشمري', nameEn: 'Dr. Noura Al-Shamri', specialty: 'طب العيون', specialtyEn: 'Ophthalmology', rating: 4.6, experience: 8, price: 400, hospitalId: hospital1.id, departmentId: 'dept-4' },
    { id: 'doc-5', name: 'د. فهد الدوسري', nameEn: 'Dr. Fahad Al-Dosari', specialty: 'طب الأسنان', specialtyEn: 'Dentistry', rating: 4.5, experience: 11, price: 300, hospitalId: hospital1.id, departmentId: 'dept-5' },
    { id: 'doc-6', name: 'د. منى الحربي', nameEn: 'Dr. Muna Al-Harbi', specialty: 'الأمراض الجلدية', specialtyEn: 'Dermatology', rating: 4.8, experience: 9, price: 380, hospitalId: hospital1.id, departmentId: 'dept-6' },
    { id: 'doc-7', name: 'د. عبدالله الغامدي', nameEn: 'Dr. Abdullah Al-Ghamdi', specialty: 'الطوارئ', specialtyEn: 'Emergency Medicine', rating: 4.7, experience: 14, price: 250, hospitalId: hospital2.id, departmentId: 'dept-7' },
    { id: 'doc-8', name: 'د. ريم العنزي', nameEn: 'Dr. Reem Al-Anazi', specialty: 'الأشعة التشخيصية', specialtyEn: 'Radiology', rating: 4.6, experience: 7, price: 420, hospitalId: hospital2.id, departmentId: 'dept-8' },
    { id: 'doc-9', name: 'د. سلطان الزهراني', nameEn: 'Dr. Sultan Al-Zahrani', specialty: 'الباطنة', specialtyEn: 'Internal Medicine', rating: 4.9, experience: 16, price: 480, hospitalId: hospital3.id, departmentId: 'dept-10' },
    { id: 'doc-10', name: 'د. هند السبيعي', nameEn: 'Dr. Hind Al-Subaie', specialty: 'النساء والتوليد', specialtyEn: 'Gynecology', rating: 4.8, experience: 13, price: 550, hospitalId: hospital3.id, departmentId: 'dept-11' },
  ]

  for (const doc of doctorData) {
    await prisma.doctor.upsert({
      where: { id: doc.id },
      update: {},
      create: {
        ...doc,
        bio: `طبيب متخصص في ${doc.specialty} بخبرة ${doc.experience} سنة`,
        bioEn: `Specialized in ${doc.specialtyEn} with ${doc.experience} years experience`,
      },
    })
  }

  // Create Schedules
  const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday']
  for (const doc of doctorData) {
    for (const day of days) {
      await prisma.schedule.create({
        data: { doctorId: doc.id, day, startTime: '09:00', endTime: '17:00' },
      })
    }
  }

  // Create Appointments
  const appointmentData = [
    { patientName: 'محمد أحمد', patientPhone: '+966-55-123-4567', date: '2026-08-16', time: '10:00', status: 'confirmed', doctorId: 'doc-1', hospitalId: hospital1.id },
    { patientName: 'فاطمة علي', patientPhone: '+966-55-234-5678', date: '2026-08-16', time: '11:00', status: 'pending', doctorId: 'doc-3', hospitalId: hospital1.id },
    { patientName: 'عبدالرحمن سعد', patientPhone: '+966-55-345-6789', date: '2026-08-16', time: '14:00', status: 'confirmed', doctorId: 'doc-9', hospitalId: hospital3.id },
    { patientName: 'نوف سلطان', patientPhone: '+966-55-456-7890', date: '2026-08-17', time: '09:30', status: 'pending', doctorId: 'doc-10', hospitalId: hospital3.id },
    { patientName: 'خالد عمر', patientPhone: '+966-55-567-8901', date: '2026-08-17', time: '10:30', status: 'cancelled', doctorId: 'doc-5', hospitalId: hospital1.id },
    { patientName: 'ريم فهد', patientPhone: '+966-55-678-9012', date: '2026-08-18', time: '13:00', status: 'confirmed', doctorId: 'doc-2', hospitalId: hospital1.id },
    { patientName: 'يوسف خالد', patientPhone: '+966-55-789-0123', date: '2026-08-18', time: '15:00', status: 'pending', doctorId: 'doc-7', hospitalId: hospital2.id },
    { patientName: 'لمياء أحمد', patientPhone: '+966-55-890-1234', date: '2026-08-19', time: '11:30', status: 'completed', doctorId: 'doc-6', hospitalId: hospital1.id },
  ]

  for (const appt of appointmentData) {
    await prisma.appointment.create({ data: appt })
  }

  // Create Admin Users
  await prisma.user.upsert({
    where: { email: 'admin@medicare.sa' },
    update: {},
    create: { name: 'مدير النظام', email: 'admin@medicare.sa', password: 'admin123', role: 'super_admin', active: true },
  })
  await prisma.user.upsert({
    where: { email: 'hospital@medicare.sa' },
    update: {},
    create: { name: 'مدير المستشفى', email: 'hospital@medicare.sa', password: 'hospital123', role: 'hospital_admin', active: true },
  })

  console.log('Seed data created successfully!')
}

main().catch((e) => { console.error(e); process.exit(1) }).finally(async () => { await prisma.$disconnect() })
