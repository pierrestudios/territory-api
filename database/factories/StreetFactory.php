<?php

namespace Database\Factories;

use App\Models\Street;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StreetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Street::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'is_apt_building' => 0,
            'street' => $this->faker->streetName,
        ];
    }
}
