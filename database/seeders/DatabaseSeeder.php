<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DiseasesTableSeeder::class);
        $this->call(DiseaseClinicalDataTableSeeder::class);
        $this->call(DiseaseSymptomsTableSeeder::class);
        $this->call(LabTestsTableSeeder::class);
        $this->call(MedicinesTableSeeder::class);
        $this->call(MigrationsTableSeeder::class);
        $this->call(OauthAccessTokensTableSeeder::class);
        $this->call(OauthClientsTableSeeder::class);
        $this->call(OauthPersonalAccessClientsTableSeeder::class);
        $this->call(PersonalAccessTokensTableSeeder::class);
        $this->call(ProceduresTableSeeder::class);
        $this->call(SymptomsTableSeeder::class);
        $this->call(SymptomMapTableSeeder::class);
        $this->call(TreatmentPlansTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UserInteractionsTableSeeder::class);
    }
}
