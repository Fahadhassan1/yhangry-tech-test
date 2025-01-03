<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_menu_cuisines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_menu_id')->constrained('set_menus')->onDelete('cascade');
            $table->foreignId('cuisine_id')->constrained('cuisines')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('set_menu_cuisines');
    }
};
