<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'users', function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('level');
                $table->string('email')->unique();
                $table->string('password', 60);
                $table->rememberToken();
                $table->timestamps();
            }
        );
        
        DB::table('users')->insert(
            [
                'email' => env('APP_ADMIN_EMAIL'),
                'password' => bcrypt(env('APP_ADMIN_PASSWORD')),
                'level' => 4
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
