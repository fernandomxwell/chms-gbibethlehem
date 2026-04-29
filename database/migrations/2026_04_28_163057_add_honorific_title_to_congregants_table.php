<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('congregants', function (Blueprint $table) {
            $table->enum('honorific_title', ['bpk', 'ibu', 'sdr', 'sdri'])->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('congregants', function (Blueprint $table) {
            $table->dropColumn('honorific_title');
        });
    }
};
