<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Kelas;

class CleanupInactiveStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-inactive-students {--confirm : Ask for confirmation before deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up inactive student records from class_student table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of inactive student records...');

        // Count inactive records first
        $inactiveCount = DB::table('class_student')
            ->where('status', 'inactive')
            ->count();

        if ($inactiveCount === 0) {
            $this->info('No inactive student records found.');
            return 0;
        }

        $this->warn("Found {$inactiveCount} inactive student records.");

        if ($this->option('confirm') || $this->confirm('Do you want to delete these inactive records? This action cannot be undone.')) {
            // Update current_students count for each class before deleting
            $classesToUpdate = DB::table('class_student')
                ->where('status', 'inactive')
                ->select('class_id', DB::raw('COUNT(*) as inactive_count'))
                ->groupBy('class_id')
                ->get();

            foreach ($classesToUpdate as $classData) {
                $class = Kelas::find($classData->class_id);
                if ($class) {
                    $newCount = max(0, $class->current_students - $classData->inactive_count);
                    $class->update(['current_students' => $newCount]);

                    $this->info("Updated class {$class->name}: reduced current_students from {$class->current_students + $classData->inactive_count} to {$newCount}");
                }
            }

            // Delete inactive records
            $deleted = DB::table('class_student')
                ->where('status', 'inactive')
                ->delete();

            $this->info("Successfully deleted {$deleted} inactive student records.");
            $this->info('All class student counts have been updated.');
        } else {
            $this->info('Cleanup cancelled.');
        }

        return 0;
    }
}
