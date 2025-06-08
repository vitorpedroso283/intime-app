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
        Schema::table('users', function (Blueprint $table) {
            $table->index('position');
            $table->index('name');
            $table->index('role');
        });

        Schema::table('punches', function (Blueprint $table) {
            $table->index('created_by');
            $table->index('punched_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['position']);
            $table->dropIndex(['name']);
            $table->dropIndex(['role']);
        });

        Schema::table('punches', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
            $table->dropIndex(['punched_at']);
            $table->dropIndex(['type']);
        });
    }
};
