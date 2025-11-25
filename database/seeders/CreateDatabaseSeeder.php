<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // Create database if it doesn't exist
            DB::statement('CREATE DATABASE IF NOT EXISTS jadwal_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $this->command->info('Database "jadwal_app" created or already exists.');
        } catch (\Exception $e) {
            $this->command->error('Error creating database: ' . $e->getMessage());
        }
    }
}