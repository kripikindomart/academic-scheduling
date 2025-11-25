<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First create the database without specifying it
        try {
            // Connect to MySQL without database
            $pdo = DB::connection('mysql')->getPdo();
            $pdo->exec("CREATE DATABASE IF NOT EXISTS jadwal_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->command->info('Database "jadwal_app" created successfully.');
        } catch (\Exception $e) {
            $this->command->error('Error creating database: ' . $e->getMessage());
            return;
        }

        // Reconnect to the new database
        config(['database.connections.mysql.database' => 'jadwal_app']);
        DB::purge('mysql');
        DB::reconnect('mysql');

        $this->command->info('Connected to database "jadwal_app".');
    }
}