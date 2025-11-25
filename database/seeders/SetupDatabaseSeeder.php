<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Run the complete SQL schema
        $schemaPath = database_path('seeders/complete_schema.sql');
        $sql = file_get_contents($schemaPath);

        // Split SQL into individual statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    DB::unprepared($statement . ';');
                    $this->command->info('Executed statement successfully');
                } catch (\Exception $e) {
                    $this->command->error('Error executing statement: ' . $e->getMessage());
                    // Continue with other statements even if one fails
                }
            }
        }

        $this->command->info('Database schema setup completed!');
    }
}