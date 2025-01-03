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
        Schema::create('set_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price_per_person', 8, 2);
            $table->decimal('min_spend', 8, 2);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_seated')->default(false);
            $table->boolean('is_standing')->default(false);
            $table->boolean('is_canape')->default(false);
            $table->boolean('is_mixed_dietary')->default(false);
            $table->boolean('is_meal_prep')->default(false);
            $table->boolean('is_halal')->default(false);
            $table->boolean('is_kosher')->default(false);
            $table->boolean('available')->default(true);
            $table->integer('number_of_orders')->default(0);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('set_menus');
    }
};
