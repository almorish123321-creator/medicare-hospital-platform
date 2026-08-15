<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['hospital_id' => 1, 'name' => 'General Medicine', 'description' => 'General health consultations', 'icon' => 'stethoscope', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Cardiology', 'description' => 'Heart and cardiovascular care', 'icon' => 'heart', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Orthopedics', 'description' => 'Bone and joint treatments', 'icon' => 'bone', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Pediatrics', 'description' => 'Children health care', 'icon' => 'baby', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Dermatology', 'description' => 'Skin and hair care', 'icon' => 'skin', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'ENT', 'description' => 'Ear, Nose, and Throat', 'icon' => 'ear', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Ophthalmology', 'description' => 'Eye care and surgery', 'icon' => 'eye', 'status' => 'active'],
            ['hospital_id' => 1, 'name' => 'Emergency', 'description' => 'Emergency medical services', 'icon' => 'emergency', 'status' => 'active'],
            ['hospital_id' => 2, 'name' => 'General Medicine', 'description' => 'General consultations', 'icon' => 'stethoscope', 'status' => 'active'],
            ['hospital_id' => 2, 'name' => 'Internal Medicine', 'description' => 'Internal medicine specialists', 'icon' => 'lungs', 'status' => 'active'],
            ['hospital_id' => 2, 'name' => 'Gynecology', 'description' => 'Women health care', 'icon' => 'female', 'status' => 'active'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
