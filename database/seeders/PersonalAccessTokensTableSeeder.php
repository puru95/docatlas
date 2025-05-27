<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalAccessTokensTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::unprepared('INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'97af836f31386edd5333ad5e359a9786120c00991189c60df78fcdd988ad647b\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 08:47:58\', \'2025-05-22 08:47:58\'),
(2, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'ef0c48f0210bbb6dc809b4898faec649ad8a55ba7bdf0b41b787d0b4cf75cee6\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:00:06\', \'2025-05-22 09:00:06\'),
(3, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'fdeb6206894e61d63d52abb8fa2d0ac7115439399adbd4d104d62bf30fda3466\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:00:27\', \'2025-05-22 09:00:27\'),
(4, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'5e1c9818de8f8c3d10783d5cc67890b26cd54ccb0f1774b8968c25bab6d27fc4\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:00:29\', \'2025-05-22 09:00:29\'),
(5, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'5d488873e2fba15f8d004645710fd1a90e0d32644b25b65e9f43271b8d127f8c\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:01:16\', \'2025-05-22 09:01:16\'),
(6, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'f408b8d1cb309e3d837ee73fd95cd56ff30f1d35873ae5db14b38c4dc1842982\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:01:46\', \'2025-05-22 09:01:46\'),
(7, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'7f6b3a286581cb93042a2d69132f9aec6ad1309213bc04492a4d842528008b14\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:18:22\', \'2025-05-22 09:18:22\'),
(8, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'14448b5e5607846f7552bdb97e70acc593d5249ddb2d807bf258bcb8565a9016\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:18:31\', \'2025-05-22 09:18:31\'),
(9, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'a994b3c9476b25274ef24b2df716cfb9f77ddb9ba63b3845eda1adf14d9e17a8\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:18:32\', \'2025-05-22 09:18:32\'),
(10, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'b90bdd47e2ab83f2136e5b0336bf67baf73b549510800ea9b8fee8fdd73adfdf\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:18:33\', \'2025-05-22 09:18:33\'),
(11, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'d3024b8a5841adef096b6613f7c5eedd39322038c09d5295f7eb613849fe3c88\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:19:10\', \'2025-05-22 09:19:10\'),
(12, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'ec30f4b8be0091204026bb9dfe1715701ffcb1975e2708eaea96137020725932\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:19:35\', \'2025-05-22 09:19:35\'),
(13, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'393a5bf1349d91077c2f2dea9ffb9cb78ffc015df41c772e62a7082cb3e3efcd\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:19:48\', \'2025-05-22 09:19:48\'),
(14, \'App\\Models\\User\', 1, \'LaravelPassportToken\', \'f4ee8246d9f9cff6eac53bbb578c1c204f71e4d998f78ac3f950748154f63f9f\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:20:00\', \'2025-05-22 09:20:00\'),
(15, \'App\\Models\\User\', 1, \'appToken\', \'ee7f0f32f7857d783202fde6adcc09380c4fd34002258985d04f3f1c34be1863\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:25:40\', \'2025-05-22 09:25:40\'),
(16, \'App\\Models\\User\', 1, \'appToken\', \'4ec57b05f284543349b4b2ce387f77430c2e12db7937071d538bf0109c402a70\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:25:55\', \'2025-05-22 09:25:55\'),
(17, \'App\\Models\\User\', 1, \'appToken\', \'9393414af7160072f61b127f4b4d6c522cb1e1883c6e2de3954cea932c428034\', \'[\"*\"]\', NULL, NULL, \'2025-05-22 09:25:57\', \'2025-05-22 09:25:57\');');
    }
}
