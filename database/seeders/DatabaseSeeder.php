<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DiseasesSeeder::class);
        // $this->call(DiseaseClinicalDataSeeder::class);
        // $this->call(DiseaseSymptomsSeeder::class);
        // $this->call(LabTestsSeeder::class);
        // $this->call(MedicinesSeeder::class);
        // $this->call(OauthAccessTokensSeeder::class);
        // $this->call(OauthClientsSeeder::class);
        // $this->call(OauthPersonalAccessClientsSeeder::class);
        // $this->call(PersonalAccessTokensSeeder::class);
        // $this->call(ProceduresSeeder::class);
        // $this->call(SymptomsSeeder::class);
        // $this->call(SymptomMapSeeder::class);
        // $this->call(TreatmentPlansSeeder::class);
        // $this->call(UsersSeeder::class);
        // $this->call(UserInteractionsSeeder::class);
    }
}
