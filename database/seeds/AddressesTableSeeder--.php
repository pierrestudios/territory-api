<?php

use Illuminate\Database\Seeder;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $addresses = $seeds[0]['addresses'];
	    foreach($addresses as $address) {
		    var_dump($address);
/*
		    DB::table('publishers')->insert([
			    'territory_id' => rand(1, 10),
	            'name' => $address,
	            'address' => $address,
	            'phone' => $address,
	        ]);
*/
	    }
	    
	    public static $seeds = json_decode(self::$data);
	    
	    public static $data = '[{"addresses":[{"id":"1","territory_id":"1","name":"Willy Demonds","address":"19101 NE 3 CT, Apt 201","phone":null,"notes":"Works Nights","dates":[{"id":"1","address_id":"1","date":"7-15-2014","notes":"A"},{"id":"2","address_id":"1","date":"7-15-2014","notes":"G,O"},{"id":"3","address_id":"1","date":"07-10-2014","notes":"G, O"}]},{"id":"2","territory_id":"1","name":"Ricco Melvin III","address":"19101 NE 3 CT, Apt 205","phone":"786-345-3309","notes":null,"dates":[]},{"id":"18","territory_id":"1","name":"William Nelson","address":"654 NE 2 Ave","phone":"","notes":null,"dates":[{"id":"8","address_id":"18","date":"07-16-2014","notes":"G"}]},{"id":"19","territory_id":"1","name":"Ronda Wendell","address":"103 NE 2 Ave","phone":"","notes":null,"dates":[{"id":"9","address_id":"19","date":"07-09-2014","notes":"F, O"}]},{"id":"20","territory_id":"1","name":"Wester Costell","address":"495 NE 2 Ave","phone":"305-344-3443","notes":null,"dates":[]},{"id":"30","territory_id":"1","name":"Sisell Wornam","address":"904 N Miami Ave","phone":"305-345-5459","notes":null,"dates":[{"id":"24","address_id":"30","date":"07-02-2014","notes":"F, R"}]},{"id":"41","territory_id":"1","name":"Timothy","address":"Samuelson","phone":null,"notes":null,"dates":[{"id":"75","address_id":"41","date":"7-30-2014","notes":"G, R"}]},{"id":"60","territory_id":"1","name":"Adam Lorrell","address":"234 NE 3 Ave","phone":"305-334-0494","notes":null,"dates":[{"id":"72","address_id":"60","date":"07-24-2014","notes":"O, G"},{"id":"73","address_id":"60","date":"7-30-2014","notes":"F \"Cindy\" Adam\'s Wife"}]}]';
	    
    }
}
