<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'territory_id' => $this->faker->randomDigitNotNull,
            'street_id' => $this->faker->randomDigitNotNull,
            'inactive' => 0,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
            'name' => $this->faker->name,
            'address' => rand(100, 50000),
            'phone' => $this->faker->phoneNumber,
            'apt' => '',
        ];
    }
}
