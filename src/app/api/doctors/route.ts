import { NextResponse } from 'next/server'
import { db } from '@/lib/db'

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url)
    const hospitalId = searchParams.get('hospitalId')
    const departmentId = searchParams.get('departmentId')
    const search = searchParams.get('search')

    const where: Record<string, unknown> = { available: true }
    if (hospitalId) where.hospitalId = hospitalId
    if (departmentId) where.departmentId = departmentId
    if (search) {
      where.OR = [
        { name: { contains: search } },
        { nameEn: { contains: search } },
        { specialty: { contains: search } },
        { specialtyEn: { contains: search } },
      ]
    }

    const doctors = await db.doctor.findMany({
      where,
      include: {
        hospital: true,
        department: true,
        schedules: true,
      },
      orderBy: { rating: 'desc' },
    })
    return NextResponse.json(doctors)
  } catch (error) {
    return NextResponse.json({ error: 'Failed to fetch doctors' }, { status: 500 })
  }
}
