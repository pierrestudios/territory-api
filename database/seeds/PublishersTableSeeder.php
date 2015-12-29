<?php

use Illuminate\Database\Seeder;

class PublishersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('publishers')->insert([
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'type' => 'regular',
        ]);
    }
}
