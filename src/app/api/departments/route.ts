import { NextResponse } from 'next/server'
import { db } from '@/lib/db'

export async function GET() {
  try {
    const departments = await db.department.findMany({
      where: { active: true },
      include: { hospital: true, doctors: true },
      orderBy: { name: 'asc' },
    })
    return NextResponse.json(departments)
  } catch (error) {
    return NextResponse.json({ error: 'Failed to fetch departments' }, { status: 500 })
  }
}
