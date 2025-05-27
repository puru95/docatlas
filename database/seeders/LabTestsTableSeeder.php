<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LabTestsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `lab_tests` (`id`, `name`, `purpose`) VALUES
(1, \'ECG\', \'Check for rhythm abnormalities\'),
(2, \'Troponin I\', \'Detects heart muscle damage\'),
(3, \'Spirometry\', \'Assess lung function\'),
(4, \'Chest X-ray\', \'Rule out pneumonia\'),
(5, \'Serum Amylase\', \'Check pancreas enzyme levels\'),
(6, \'Ultrasound\', \'Detect gallstones\'),
(7, \'Stress Test\', \'Assess heart function under stress\'),
(8, \'Coronary Angiography\', \'Visualize blood flow in coronary arteries\'),
(9, \'Blood Pressure Monitoring\', \'Track blood pressure levels\'),
(10, \'Electrolyte Panel\', \'Assess kidney and electrolyte status\'),
(11, \'INR\', \'Monitor blood clotting levels\'),
(12, \'Echocardiogram\', \'Visualize valve function\'),
(13, \'BNP Test\', \'Measure severity of heart failure\'),
(14, \'Peak Flow Meter\', \'Track airflow limitation\'),
(15, \'CBC\', \'Check for infection markers\'),
(16, \'D-dimer\', \'Screen for blood clot\'),
(17, \'CT Pulmonary Angiogram\', \'Confirm clot presence\'),
(18, \'Sputum Test\', \'Detect TB bacteria\'),
(19, \'CRP Test\', \'Check for inflammation\'),
(20, \'HRCT\', \'Detect interstitial changes\'),
(21, \'Pulmonary Function Test\', \'Measure lung capacity\'),
(22, \'Serum Lipase\', \'Evaluate pancreatic function\'),
(23, \'CT Scan\', \'Visualize pancreas structure\'),
(24, \'CA 19-9\', \'Tumor marker\'),
(25, \'Abdominal MRI\', \'Detailed imaging of pancreas\'),
(26, \'Endoscopy\', \'Visual confirmation\'),
(27, \'pH Monitoring\', \'Measure acid exposure\'),
(28, \'LFT\', \'Assess liver function\'),
(29, \'H. pylori test\', \'Detect infection\');');
    }
}
