<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, \'2014_10_12_000000_create_users_table\', 1),
(2, \'2014_10_12_100000_create_password_reset_tokens_table\', 1),
(3, \'2016_06_01_000001_create_oauth_auth_codes_table\', 1),
(4, \'2016_06_01_000002_create_oauth_access_tokens_table\', 1),
(5, \'2016_06_01_000003_create_oauth_refresh_tokens_table\', 1),
(6, \'2016_06_01_000004_create_oauth_clients_table\', 1),
(7, \'2016_06_01_000005_create_oauth_personal_access_clients_table\', 1),
(8, \'2019_08_19_000000_create_failed_jobs_table\', 1),
(9, \'2019_12_14_000001_create_personal_access_tokens_table\', 1),
(10, \'2025_05_26_104706_create_user_interactions_table\', 2);');
    }
}
