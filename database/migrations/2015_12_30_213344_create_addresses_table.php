<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'addresses', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('territory_id');
                $table->integer('street_id');
                $table->tinyInteger('inactive');
                $table->float('lat', 10, 6);
                $table->float('long', 10, 6);
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->integer('address');
                $table->string('apt')->nullable();
                $table->timestamps();
                $table->unique(array('address', 'street_id', 'apt'));
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
