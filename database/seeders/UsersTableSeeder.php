<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `users` (`id`, `name`, `email`, `mobileNumber`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, \'Admin\', \'admin@treint.com\', 9999999999, NULL, \'$2y$12$h5m.WDtWDwV1BFv624gR6uONRewvs8TFrgfTjUOIBYolvXYNapRQC\', NULL, \'2025-05-22 09:33:45\', \'2025-05-22 09:33:45\');');
    }
}
