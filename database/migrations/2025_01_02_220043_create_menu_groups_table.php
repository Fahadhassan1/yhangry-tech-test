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
        Schema::create('menu_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_menu_id')->constrained('set_menus')->onDelete('cascade');
            $table->integer('dishes_count');
            $table->integer('selectable_dishes_count');
            $table->integer('group_ungrouped')->default(0);
            $table->integer('group_mains')->default(0);
            $table->integer('group_starter')->default(0);
            $table->integer('group_desserts')->default(0);
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
        Schema::dropIfExists('menu_groups');
    }
};
