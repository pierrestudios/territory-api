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
        $faker = Faker\Factory::create();
        $territoryId = DB::table('territories')->insertGetId(
            [
                'publisher_id' => rand(1, 5),
                'assigned_date' => '2017-'. rand(1, 12) .'-'. rand(1, 30), 
                'number' => rand(1, 100),
                'location' => str_random(15),
                'boundaries' => 'boundaries',
            ]
        );

        // Build addresses with Home types
        factory(\App\Street::class, 5)->create()->each(
            function ($s) use ($territoryId) {
                factory(\App\Address::class, 10)->create(
                    [
                        'territory_id' => $territoryId,
                        'street_id' => $s->id,
                    ]
                )->each(
                    function ($a) {
                        $a->notes()->save(
                            factory(\App\Note::class)->make(
                                [
                                    'content' => 'Absent',
                                ]
                            )
                        );
                    }
                );
            }
        );

        // Build addresses with Apt types
        for ($i = 0; $i < 2; $i++) {
            factory(\App\Street::class)->create(
                [
                    'is_apt_building' => 1,
                    'street' => rand(100, 200) . ' ' . $faker->streetName,
                ]
            )->each(
                function ($s) use ($territoryId) {
                    factory(\App\Address::class, 10)->create(
                        [
                            'territory_id' => $territoryId,
                            'street_id' => $s->id,
                        ]
                    )->each(
                        function ($a) {
                            $a->notes()->save(
                                factory(\App\Note::class)->make(
                                    [
                                        'content' => 'Absent',
                                    ]
                                )
                            );
                        }
                    );
                }
            );
        }
    }
}
