<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserInteractionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `user_interactions` (`id`, `user_id`, `thread_id`, `sequence_no`, `question`, `options`, `answers`, `created_at`, `updated_at`) VALUES
(1, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 1, \'How long have you been experiencing these symptoms?\', \'[\"Less than 3 days\",\"- 3\\u20137 days\",\"- 1\\u20132 weeks\",\"- More than 2 weeks\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(2, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 2, \'Have you had any recent contact with someone who has a confirmed illness, like the flu or COVID-19?\', \'[\"Yes, with someone who had the flu\",\"- Yes, with someone who had COVID-19\",\"- Yes, with someone who had another illness\",\"- No known contact\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(3, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 3, \'Are you experiencing any breathing difficulties or chest pain?\', \'[\"No breathing problems or chest pain\",\"- Mild breathing difficulty but no chest pain\",\"- Moderate to severe breathing difficulty\",\"- Chest pain present\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(4, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 4, \'Do you have any history of chronic health conditions such as asthma or allergies?\', \'[\"Asthma\",\"- Allergies\",\"- Both asthma and allergies\",\"- No chronic conditions\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(5, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 5, \'Have you recently traveled to any areas experiencing outbreaks of respiratory illnesses?\', \'[\"Yes, within the last two weeks\",\"- Yes, within the last month\",\"- Yes, but more than a month ago\",\"- No recent travel\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(6, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 6, \'Are you taking any medications currently?\', \'[\"Yes, prescribed medication\",\"- Yes, over-the-counter medication\",\"- Yes, both\",\"- No, not taking any medications\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(7, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 7, \'What\\'s your current temperature, if you know it?\', \'[\"Below 99\\u00b0F (37.2\\u00b0C)\",\"- 99\\u00b0F\\u2013100.4\\u00b0F (37.2\\u00b0C\\u201338\\u00b0C)\",\"- 100.5\\u00b0F\\u2013102\\u00b0F (38\\u00b0C\\u201339\\u00b0C)\",\"- Above 102\\u00b0F (39\\u00b0C)\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(8, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 8, \'Do you have any other symptoms, such as body aches or chills?\', \'[\"Body aches\",\"- Chills\",\"- Both body aches and chills\",\"- No other symptoms\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(9, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 9, \'Have these symptoms affected your daily activities or ability to work?\', \'[\"No, not affected\",\"- Yes, somewhat affected\",\"- Yes, significantly affected\",\"- Unable to work or perform daily activities\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(10, 0, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 10, \'Are you vaccinated against the flu or COVID-19?\', \'[\"Yes, for both the flu and COVID-19\",\"- Yes, only for the flu\",\"- Yes, only for COVID-19\",\"- No vaccinations\"]\', NULL, NULL, NULL),
(11, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 1, \'How long have you been experiencing these symptoms?\', \'[\"Less than 3 days\",\"- 3\\u20137 days\",\"- 1\\u20132 weeks\",\"- More than 2 weeks\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(12, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 2, \'Have you had any recent contact with someone who has a confirmed illness, like the flu or COVID-19?\', \'[\"Yes, with someone who had the flu\",\"- Yes, with someone who had COVID-19\",\"- Yes, with someone who had another illness\",\"- No known contact\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(13, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 3, \'Are you experiencing any breathing difficulties or chest pain?\', \'[\"No breathing problems or chest pain\",\"- Mild breathing difficulty but no chest pain\",\"- Moderate to severe breathing difficulty\",\"- Chest pain present\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(14, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 4, \'Do you have any history of chronic health conditions such as asthma or allergies?\', \'[\"Asthma\",\"- Allergies\",\"- Both asthma and allergies\",\"- No chronic conditions\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(15, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 5, \'Have you recently traveled to any areas experiencing outbreaks of respiratory illnesses?\', \'[\"Yes, within the last two weeks\",\"- Yes, within the last month\",\"- Yes, but more than a month ago\",\"- No recent travel\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(16, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 6, \'Are you taking any medications currently?\', \'[\"Yes, prescribed medication\",\"- Yes, over-the-counter medication\",\"- Yes, both\",\"- No, not taking any medications\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(17, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 7, \'What\\'s your current temperature, if you know it?\', \'[\"Below 99\\u00b0F (37.2\\u00b0C)\",\"- 99\\u00b0F\\u2013100.4\\u00b0F (37.2\\u00b0C\\u201338\\u00b0C)\",\"- 100.5\\u00b0F\\u2013102\\u00b0F (38\\u00b0C\\u201339\\u00b0C)\",\"- Above 102\\u00b0F (39\\u00b0C)\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(18, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 8, \'Do you have any other symptoms, such as body aches or chills?\', \'[\"Body aches\",\"- Chills\",\"- Both body aches and chills\",\"- No other symptoms\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(19, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 9, \'Have these symptoms affected your daily activities or ability to work?\', \'[\"No, not affected\",\"- Yes, somewhat affected\",\"- Yes, significantly affected\",\"- Unable to work or perform daily activities\",\"- Other (please specify)\"]\', NULL, NULL, NULL),
(20, 1, \'thread_oqenEwMDjb8UXkXw73hHHWTQ\', 10, \'Are you vaccinated against the flu or COVID-19?\', \'[\"Yes, for both the flu and COVID-19\",\"- Yes, only for the flu\",\"- Yes, only for COVID-19\",\"- No vaccinations\"]\', NULL, NULL, NULL);');
    }
}
