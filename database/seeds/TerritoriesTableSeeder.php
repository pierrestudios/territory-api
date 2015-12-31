<?php

use Illuminate\Database\Seeder;

class TerritoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('territories')->insert([
            'publisher_id' => rand(1,5),
            'number' => rand(1,100),
            'location' => str_random(15),
            'boundaries' => 'boundaries',
        ]);
    }
}
