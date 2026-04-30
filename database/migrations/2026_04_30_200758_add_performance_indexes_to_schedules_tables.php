<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('congregant_schedules', function (Blueprint $table) {
            $table->index('congregant_id');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['activity_id', 'service_type_id']);
        });
    }

    public function down(): void
    {
        Schema::table('congregant_schedules', function (Blueprint $table) {
            $table->dropIndex(['congregant_id']);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['activity_id', 'service_type_id']);
        });
    }
};
