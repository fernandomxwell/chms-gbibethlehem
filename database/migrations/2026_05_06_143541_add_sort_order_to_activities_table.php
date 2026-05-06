<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->unsignedBigInteger('sort_order')->default(0)->after('id')->index();
        });

        DB::statement('
            UPDATE activities
            SET sort_order = (
                SELECT COUNT(*) FROM activities a2
                WHERE a2.id <= activities.id
                AND a2.deleted_at IS NULL
            )
            WHERE deleted_at IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
