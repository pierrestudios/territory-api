<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerritoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('territories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('publisher_id')->nullable();
            $table->date('assigned_date');
            $table->integer('number')->nullable()->unique();
            $table->mediumText('location')->nullable();
            $table->text('boundaries')->nullable();
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
        Schema::drop('territories');
    }
}
