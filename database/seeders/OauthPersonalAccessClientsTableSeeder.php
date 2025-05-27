<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthPersonalAccessClientsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, \'2025-05-22 09:40:32\', \'2025-05-22 09:40:32\');');
    }
}
