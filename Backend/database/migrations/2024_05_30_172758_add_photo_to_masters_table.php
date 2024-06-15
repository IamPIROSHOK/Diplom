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
        Schema::table('masters', function (Blueprint $table) {
            $table->string('photo')->nullable(); // Добавление нового поля
        });
    }

    public function down()
    {
        Schema::table('masters', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
