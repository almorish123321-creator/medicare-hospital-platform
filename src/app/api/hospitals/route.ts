import { NextResponse } from 'next/server'
import { db } from '@/lib/db'

export async function GET() {
  try {
    const hospitals = await db.hospital.findMany({
      where: { active: true },
      include: {
        departmentsList: { where: { active: true } },
        doctorsList: { where: { available: true }, take: 6 },
      },
      orderBy: { rating: 'desc' },
    })
    return NextResponse.json(hospitals)
  } catch (error) {
    return NextResponse.json({ error: 'Failed to fetch hospitals' }, { status: 500 })
  }
}
