<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('migrations')->insert([
            [
                'migration' => '2025_04_12_XXXXXX_create_notifications_table',
                'batch' => 1
            ],
            [
                'migration' => '2025_04_12_XXXXXX_create_notification_targets_table',
                'batch' => 1
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
