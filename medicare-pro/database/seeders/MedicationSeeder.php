<?php

namespace Database\Seeders;

use App\Models\Medication;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            ['name' => 'Paracetamol', 'generic_name' => 'Acetaminophen', 'category' => 'Analgesic', 'stock_quantity' => 500, 'unit' => 'Tablet', 'price' => 5.00, 'expiry_date' => '2026-12-31', 'status' => 'available'],
            ['name' => 'Amoxicillin', 'generic_name' => 'Amoxicillin Trihydrate', 'category' => 'Antibiotic', 'stock_quantity' => 200, 'unit' => 'Capsule', 'price' => 12.00, 'expiry_date' => '2026-06-30', 'status' => 'available'],
            ['name' => 'Ibuprofen', 'generic_name' => 'Ibuprofen', 'category' => 'NSAID', 'stock_quantity' => 350, 'unit' => 'Tablet', 'price' => 8.00, 'expiry_date' => '2026-09-30', 'status' => 'available'],
            ['name' => 'Omeprazole', 'generic_name' => 'Omeprazole', 'category' => 'PPI', 'stock_quantity' => 150, 'unit' => 'Capsule', 'price' => 15.00, 'expiry_date' => '2026-03-31', 'status' => 'available'],
            ['name' => 'Metformin', 'generic_name' => 'Metformin HCl', 'category' => 'Antidiabetic', 'stock_quantity' => 300, 'unit' => 'Tablet', 'price' => 10.00, 'expiry_date' => '2026-11-30', 'status' => 'available'],
            ['name' => 'Cetirizine', 'generic_name' => 'Cetirizine HCl', 'category' => 'Antihistamine', 'stock_quantity' => 8, 'unit' => 'Tablet', 'price' => 6.00, 'expiry_date' => '2026-08-15', 'status' => 'low_stock'],
            ['name' => 'Aspirin', 'generic_name' => 'Acetylsalicylic Acid', 'category' => 'Analgesic', 'stock_quantity' => 0, 'unit' => 'Tablet', 'price' => 3.00, 'expiry_date' => '2027-01-15', 'status' => 'out_of_stock'],
            ['name' => 'Lisinopril', 'generic_name' => 'Lisinopril', 'category' => 'ACE Inhibitor', 'stock_quantity' => 100, 'unit' => 'Tablet', 'price' => 20.00, 'expiry_date' => '2025-01-01', 'status' => 'expired'],
        ];

        foreach ($medications as $med) {
            Medication::create([
                ...$med,
                'hospital_id' => 1,
            ]);
        }
    }
}
