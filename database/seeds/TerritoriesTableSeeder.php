<?php

use Illuminate\Database\Seeder;
use App\Models\Street;
use App\Models\Address;
use App\Models\Note;

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
        Street::factory()
            ->times(5)
            ->create()
            ->each(
            function ($s) use ($territoryId) {
                Address::factory()
                ->times(5)->create(
                    [
                        'territory_id' => $territoryId,
                        'street_id' => $s->id,
                    ]
                )->each(
                    function ($a) {
                        $a->notes()->save(
                            Note::factory()->make(
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
            Street::factory()->create(
                [
                    'is_apt_building' => 1,
                    'street' => rand(100, 200) . ' ' . $faker->streetName,
                ]
            )->each(
                function ($s) use ($territoryId) {
                    Address::factory()->create(
                        [
                            'territory_id' => $territoryId,
                            'street_id' => $s->id,
                        ]
                    )->each(
                        function ($a) {
                            $a->notes()->save(
                                Note::factory()->make(
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
