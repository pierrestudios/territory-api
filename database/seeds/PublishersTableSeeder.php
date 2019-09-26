<?php

use Illuminate\Database\Seeder;

class PublishersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = Faker\Factory::create();

        DB::table('publishers')->insert(
            [
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'type' => 'regular',
            ]
        );
    }
}
