<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\QueueLog;
use App\Models\Review;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = ['completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'cancelled', 'no_show', 'pending', 'confirmed', 'checked_in', 'in_progress'];
        $doctors = \App\Models\Doctor::whereHas('department', fn($q) => $q->where('hospital_id', 1))->get();
        $patients = Patient::all();

        // Create appointments for the past 30 days
        for ($i = 0; $i < 50; $i++) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            $status = $statuses[array_rand($statuses)];
            $date = now()->subDays(rand(0, 30));

            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'hospital_id' => 1,
                'department_id' => $doctor->department_id,
                'appointment_date' => $date->toDateString(),
                'appointment_time' => $date->format('H:i'),
                'queue_number' => rand(1, 50),
                'status' => $status,
                'type' => rand(0, 1) ? 'booked' : 'walk_in',
                'symptoms' => collect(['Headache', 'Fever', 'Cough', 'Back pain', 'Chest pain', 'Skin rash', 'Stomach pain'])->random(),
                'notes' => null,
                'checked_in_at' => in_array($status, ['checked_in', 'in_progress', 'completed']) ? $date->copy()->addMinutes(rand(5, 30)) : null,
                'started_at' => in_array($status, ['in_progress', 'completed']) ? $date->copy()->addMinutes(rand(15, 45)) : null,
                'completed_at' => $status === 'completed' ? $date->copy()->addMinutes(rand(30, 90)) : null,
            ]);

            // Create completed appointment details
            if ($status === 'completed') {
                // Medical Record
                $medicalRecord = MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'appointment_id' => $appointment->id,
                    'doctor_id' => $doctor->id,
                    'vital_signs' => [
                        'blood_pressure' => rand(100, 140) . '/' . rand(60, 90),
                        'temperature' => (float) (36 + rand(0, 3) + rand(0, 9) / 10),
                        'weight' => rand(50, 100) . '.' . rand(0, 9),
                        'height' => rand(150, 190) . '.' . rand(0, 9),
                        'heart_rate' => rand(60, 100),
                    ],
                    'symptoms' => $appointment->symptoms,
                    'diagnosis' => collect(['Common cold', 'Hypertension', 'Diabetes follow-up', 'Muscle strain', 'Allergic reaction'])->random(),
                    'notes' => 'Follow up in 2 weeks.',
                ]);

                // Prescription
                $prescription = Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'diagnosis' => $medicalRecord->diagnosis,
                    'instructions' => 'Take as prescribed. Complete the course.',
                    'status' => collect(['dispensed', 'pending'])->random(),
                ]);

                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medication_name' => collect(['Paracetamol 500mg', 'Amoxicillin 250mg', 'Ibuprofen 400mg', 'Omeprazole 20mg', 'Metformin 500mg'])->random(),
                    'dosage' => collect(['1 tablet twice daily', '1 capsule three times daily', '2 tablets daily'])->random(),
                    'duration' => collect(['5 days', '7 days', '10 days', '14 days', '30 days'])->random(),
                    'instructions' => 'Take after meals with water.',
                ]);

                // Invoice
                Invoice::create([
                    'patient_id' => $patient->id,
                    'appointment_id' => $appointment->id,
                    'hospital_id' => 1,
                    'amount' => $doctor->consultation_fee,
                    'discount' => 0,
                    'tax' => round($doctor->consultation_fee * 0.15, 2),
                    'total_amount' => round($doctor->consultation_fee * 1.15, 2),
                    'status' => collect(['paid', 'pending'])->random(),
                    'payment_method' => collect(['cash', 'card', 'online'])->random(),
                ]);

                // Review (50% chance)
                if (rand(0, 1)) {
                    Review::create([
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'appointment_id' => $appointment->id,
                        'rating' => rand(3, 5),
                        'comment' => collect(['Excellent doctor!', 'Very professional.', 'Good experience.', 'Highly recommended.'])->random(),
                        'is_approved' => true,
                    ]);
                }
            }

            // Queue Log
            if (in_array($status, ['checked_in', 'in_progress', 'completed'])) {
                QueueLog::create([
                    'appointment_id' => $appointment->id,
                    'queue_number' => $appointment->queue_number,
                    'status' => $status === 'completed' ? 'completed' : ($status === 'in_progress' ? 'in_progress' : 'waiting'),
                    'estimated_wait_time' => rand(10, 45),
                    'called_at' => $appointment->started_at,
                ]);
            }
        }

        // Create notifications
        foreach (\App\Models\User::inRandomOrder()->take(20)->get() as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => collect(['appointment_reminder', 'queue_update', 'system'])->random(),
                'title' => collect(['Appointment Reminder', 'Queue Update', 'System Message'])->random(),
                'message' => 'This is a demo notification.',
                'is_read' => rand(0, 1),
                'read_at' => rand(0, 1) ? now() : null,
            ]);
        }
    }
}
