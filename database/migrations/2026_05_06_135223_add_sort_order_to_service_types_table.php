<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->unsignedBigInteger('sort_order')->default(0)->after('id')->index();
        });

        DB::statement('
            UPDATE service_types
            SET sort_order = (
                SELECT COUNT(*) FROM service_types s2
                WHERE s2.id <= service_types.id
                AND s2.deleted_at IS NULL
            )
            WHERE deleted_at IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
