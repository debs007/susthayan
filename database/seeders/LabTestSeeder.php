<?php

namespace Database\Seeders;

use App\Models\LabTest;
use App\Models\LabTestCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LabTestSeeder extends Seeder
{
    /**
     * Every test name here is a standard, widely-offered Indian diagnostic
     * test - nothing invented. Prices are ROUND PLACEHOLDER NUMBERS, not
     * researched real-world pricing - review and adjust every single one
     * in the admin panel before this catalog goes live. Getting a medical
     * test's price wrong is a different order of problem than a wrong
     * product price, so treat none of these as final.
     *
     * 'center' => true means requires_center_visit (imaging/equipment-based
     * tests); false means a phlebotomist/technician can do it at the
     * customer's home (sample-collection tests).
     */
    public function run(): void
    {
        $catalog = [
            'Blood Tests' => [
                ['Complete Blood Count (CBC)', 400, false, 'Blood', null],
                ['Blood Sugar - Fasting', 150, false, 'Blood', '8-10 hours fasting required'],
                ['Blood Sugar - Post Prandial (PP)', 150, false, 'Blood', '2 hours after a meal'],
                ['HbA1c (Glycated Hemoglobin)', 600, false, 'Blood', null],
                ['Lipid Profile', 700, false, 'Blood', '10-12 hours fasting required'],
                ['Liver Function Test (LFT)', 800, false, 'Blood', '8 hours fasting recommended'],
                ['Kidney Function Test (KFT)', 800, false, 'Blood', null],
                ['Thyroid Profile (T3, T4, TSH)', 600, false, 'Blood', null],
                ['Vitamin D (25-OH)', 1200, false, 'Blood', null],
                ['Vitamin B12', 900, false, 'Blood', null],
                ['Iron Studies', 900, false, 'Blood', null],
                ['Blood Grouping & Rh Typing', 300, false, 'Blood', null],
                ['HIV Screening', 500, false, 'Blood', null],
                ['Widal Test (Typhoid)', 300, false, 'Blood', null],
                ['Dengue NS1/IgG/IgM Panel', 1500, false, 'Blood', null],
            ],
            'Urine Tests' => [
                ['Urine Routine & Microscopy', 200, false, 'Urine', null],
                ['Urine Culture & Sensitivity', 600, false, 'Urine', 'Midstream sample'],
                ['Urine Pregnancy Test', 150, false, 'Urine', null],
                ['24-Hour Urine Protein', 500, false, 'Urine', 'Collected over 24 hours - instructions provided at booking'],
                ['Urine Microalbumin', 500, false, 'Urine', null],
            ],
            'Cardiac (Heart)' => [
                ['ECG (Electrocardiogram)', 400, true, null, null],
                ['2D Echo (Echocardiogram)', 2000, true, null, null],
                ['TMT (Treadmill Test / Stress Test)', 2500, true, null, 'Wear comfortable clothing and shoes'],
                ['Lipid Profile (Cardiac Risk Panel)', 700, false, 'Blood', '10-12 hours fasting required'],
                ['Troponin-I (Cardiac Marker)', 1200, false, 'Blood', null],
            ],
            'Pulmonology (Lungs)' => [
                ['Pulmonary Function Test (PFT / Spirometry)', 1000, true, null, 'Avoid heavy meals before the test'],
                ['Chest X-Ray', 500, true, null, null],
                ['Sputum Culture & Sensitivity', 700, false, 'Sputum', 'Early morning sample preferred'],
                ['Sleep Study (Polysomnography)', 6000, true, null, 'Overnight center visit required'],
            ],
            'X-Ray & Imaging' => [
                ['X-Ray - Chest', 500, true, null, null],
                ['X-Ray - Abdomen', 500, true, null, null],
                ['X-Ray - Spine', 600, true, null, null],
                ['X-Ray - Limb/Joint', 500, true, null, null],
                ['MRI - Brain', 6000, true, null, 'Remove all metal objects before the scan'],
                ['MRI - Spine', 7000, true, null, 'Remove all metal objects before the scan'],
                ['MRI - Knee', 6500, true, null, 'Remove all metal objects before the scan'],
                ['CT Scan - Brain', 4000, true, null, null],
                ['CT Scan - Abdomen', 5000, true, null, 'Fasting may be required - confirm at booking'],
                ['Ultrasound - Abdomen', 1200, true, null, 'Fasting may be required - confirm at booking'],
                ['Ultrasound - Pregnancy', 1500, true, null, null],
            ],
        ];

        foreach ($catalog as $categoryName => $tests) {
            $category = LabTestCategory::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'is_active' => true]
            );

            foreach ($tests as [$name, $price, $requiresCenter, $sampleType, $prep]) {
                LabTest::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'lab_test_category_id' => $category->id,
                        'name' => $name,
                        'price' => $price,
                        'requires_center_visit' => $requiresCenter,
                        'sample_type' => $sampleType,
                        'preparation_instructions' => $prep,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
