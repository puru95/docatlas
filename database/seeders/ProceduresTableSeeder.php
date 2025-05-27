<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProceduresTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `procedures` (`id`, `name`, `type`, `disease_id`) VALUES
(1, \'Emergency admission\', \'Supportive\', 1),
(2, \'Complete bed rest for 48 hours\', \'Supportive\', 1),
(3, \'Oxygen therapy if needed\', \'Supportive\', NULL),
(4, \'Pulmonary rehabilitation\', \'Supportive\', 10),
(5, \'Vaccination for flu and pneumonia\', \'Supportive\', 10),
(6, \'Oxygen therapy for advanced stages\', \'Supportive\', NULL),
(7, \'Fasting to rest the pancreas\', \'Supportive\', NULL),
(8, \'IV fluids\', \'Supportive\', NULL),
(9, \'Hospitalization for observation\', \'Supportive\', NULL),
(10, \'Avoid strenuous activity\', \'Supportive\', NULL),
(11, \'Carry nitroglycerin\', \'Supportive\', NULL),
(12, \'Follow a heart-healthy diet\', \'Supportive\', NULL),
(13, \'Adopt DASH diet\', \'Supportive\', NULL),
(14, \'Limit alcohol\', \'Supportive\', NULL),
(15, \'Reduce stress\', \'Supportive\', NULL),
(16, \'Avoid caffeine\', \'Supportive\', NULL),
(17, \'Monitor heart rate\', \'Supportive\', NULL),
(18, \'Regular follow-up\', \'Supportive\', NULL),
(19, \'Low-sodium diet\', \'Supportive\', NULL),
(20, \'Daily weight monitoring\', \'Supportive\', NULL),
(21, \'Elevate legs when resting\', \'Supportive\', NULL),
(22, \'Bed rest\', \'Supportive\', NULL),
(23, \'Avoid exertion\', \'Supportive\', NULL),
(24, \'Stay hydrated\', \'Supportive\', NULL),
(25, \'Routine follow-up\', \'Supportive\', NULL),
(26, \'Avoid dehydration\', \'Supportive\', NULL),
(27, \'Limit caffeine\', \'Supportive\', NULL),
(28, \'Avoid allergens\', \'Supportive\', NULL),
(29, \'Warm up before exercise\', \'Supportive\', NULL),
(30, \'Use inhalers correctly\', \'Supportive\', NULL),
(31, \'Drink plenty of fluids\', \'Supportive\', NULL),
(32, \'Complete full antibiotic course\', \'Supportive\', NULL),
(33, \'Hospitalization\', \'Supportive\', NULL),
(34, \'Leg elevation\', \'Supportive\', NULL),
(35, \'Regular INR monitoring\', \'Supportive\', NULL),
(36, \'Isolate during treatment\', \'Supportive\', NULL),
(37, \'Complete 6-month course\', \'Supportive\', NULL),
(38, \'Report side effects\', \'Supportive\', NULL),
(39, \'Use humidifier\', \'Supportive\', NULL),
(40, \'Rest well\', \'Supportive\', NULL),
(41, \'Avoid exposure to dust/fumes\', \'Supportive\', NULL),
(42, \'Use supplemental oxygen\', \'Supportive\', NULL),
(43, \'Join pulmonary rehab\', \'Supportive\', NULL),
(44, \'Low-fat diet\', \'Supportive\', NULL),
(45, \'Avoid alcohol\', \'Supportive\', NULL),
(46, \'Take enzymes with meals\', \'Supportive\', NULL),
(47, \'Nutritional support\', \'Supportive\', NULL),
(48, \'Palliative care\', \'Supportive\', NULL),
(49, \'Follow-up imaging\', \'Supportive\', NULL),
(50, \'Avoid large meals\', \'Supportive\', NULL),
(51, \'Elevate head during sleep\', \'Supportive\', NULL),
(52, \'Lose excess weight\', \'Supportive\', NULL),
(53, \'Consider cholecystectomy\', \'Supportive\', NULL),
(54, \'Avoid fried foods\', \'Supportive\', NULL),
(55, \'Avoid spicy food\', \'Supportive\', NULL),
(56, \'Take full antibiotic course\', \'Supportive\', NULL),
(57, \'Avoid NSAIDs\', \'Supportive\', NULL);');
    }
}
