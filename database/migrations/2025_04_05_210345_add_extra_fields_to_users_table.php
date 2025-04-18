<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('number_cellphone')->nullable();
            $table->string('number_phone')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('level')->nullable();
            $table->boolean('active')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['number_cellphone', 'number_phone', 'neighborhood', 'city', 'address', 'level']);
        });
    }
}
