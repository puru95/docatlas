<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthClientsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, \'Laravel Personal Access Client\', \'ewCYsa9F6MxtFtqIPvz1wwgPA9Un9LYSUA0orMlV\', NULL, \'http://localhost\', 1, 0, 0, \'2025-05-22 09:40:32\', \'2025-05-22 09:40:32\'),
(2, NULL, \'Laravel Password Grant Client\', \'oLIErWI3YRnZg6WXvWSwg729SI7G522qqPpjoCpX\', \'users\', \'http://localhost\', 0, 1, 0, \'2025-05-22 09:40:32\', \'2025-05-22 09:40:32\');');
    }
}
