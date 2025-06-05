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
            $table->string('cpf')->unique()->after('name');
            $table->string('role')->after('password');
            $table->string('position')->after('role');
            $table->date('birth_date')->after('position');
            $table->string('zipcode');
            $table->string('street');
            $table->string('neighborhood')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('number')->nullable();
            $table->string('complement')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->after('remember_token');

            $table->softDeletes();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'cpf',
                'role',
                'position',
                'birth_date',
                'zipcode',
                'street',
                'neighborhood',
                'city',
                'state',
                'number',
                'complement',
                'created_by',
                'deleted_at',
            ]);
        });
    }
};
