<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create WhatsApp storage directories
        $directories = [
            storage_path('whatsapp'),
            storage_path('whatsapp/sessions'),
            storage_path('whatsapp/media'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove WhatsApp storage directories
        $directories = [
            storage_path('whatsapp/sessions'),
            storage_path('whatsapp/media'),
            storage_path('whatsapp'),
        ];

        foreach (array_reverse($directories) as $directory) {
            if (File::exists($directory)) {
                File::deleteDirectory($directory);
            }
        }
    }
};
