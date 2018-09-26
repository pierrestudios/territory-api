<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
		$this->call(PublishersTableSeeder::class);
    	$this->call(TerritoriesTableSeeder::class);
    	$this->call(AddressesTableSeeder::class);
    }
}
