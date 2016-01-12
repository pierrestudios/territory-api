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
/*
	    $seedData = '[{"addresses":[{"id":"1","territory_id":"1","name":"Willy Demonds","address":"19101 NE 3 CT, Apt 201","phone":null,"notes":"Works Nights","dates":[{"id":"1","address_id":"1","date":"7-15-2014","notes":"A"},{"id":"2","address_id":"1","date":"7-15-2014","notes":"G,O"},{"id":"3","address_id":"1","date":"07-10-2014","notes":"G, O"}]},{"id":"2","territory_id":"1","name":"Ricco Melvin III","address":"19101 NE 3 CT, Apt 205","phone":"786-345-3309","notes":null,"dates":[]},{"id":"18","territory_id":"1","name":"William Nelson","address":"654 NE 2 Ave","phone":"","notes":null,"dates":[{"id":"8","address_id":"18","date":"07-16-2014","notes":"G"}]},{"id":"19","territory_id":"1","name":"Ronda Wendell","address":"103 NE 2 Ave","phone":"","notes":null,"dates":[{"id":"9","address_id":"19","date":"07-09-2014","notes":"F, O"}]},{"id":"20","territory_id":"1","name":"Wester Costell","address":"495 NE 2 Ave","phone":"305-344-3443","notes":null,"dates":[]},{"id":"30","territory_id":"1","name":"Sisell Wornam","address":"904 N Miami Ave","phone":"305-345-5459","notes":null,"dates":[{"id":"24","address_id":"30","date":"07-02-2014","notes":"F, R"}]},{"id":"41","territory_id":"1","name":"Timothy","address":"Samuelson","phone":null,"notes":null,"dates":[{"id":"75","address_id":"41","date":"7-30-2014","notes":"G, R"}]},{"id":"60","territory_id":"1","name":"Adam Lorrell","address":"234 NE 3 Ave","phone":"305-334-0494","notes":null,"dates":[{"id":"72","address_id":"60","date":"07-24-2014","notes":"O, G"},{"id":"73","address_id":"60","date":"7-30-2014","notes":"F, Cindy, Adam Wife"}]}]}]';
	    
	    $seeds = json_decode($seedData);
	    // var_dump('$seeds');
	    // var_dump($seeds);
	    $addresses = $seeds[0]->addresses;
	    foreach($addresses as $order => $address) {
		    // var_dump($address);

		    $address_id = DB::table('addresses')->insertGetId([
			    // 'id' => $address_id,
			    'territory_id' => rand(1, 10),
	            'name' => $address->name,
	            'order' => $order,
	            'address' => strtoupper($address->address),
	            'phone' => $address->phone ? $address->phone : '',
	        ]);
	        
	        // var_dump($address_id); exit;
 	        
	        if (!empty($address->dates)) {
		        foreach($address->dates as $date) {
			         DB::table('notes')->insert([
					    'entity_id' => $address_id,
			            'date' => $date->date,
			            'content' => strtoupper($date->notes),
			            'entity' => 'Address',
			        ]);
		        }
	        }
 
	    } 
*/
		
		$seederQ = "INSERT INTO `addresses` (`id`, `territory_id`, `street_id`, `inactive`, `order`, `name`, `phone`, `address`, `created_at`, `updated_at`) VALUES
					(15, 9, 1, NULL, 0, 'Willy Demonds', '', 'APT 201', '2016-01-08 13:35:37', '0000-00-00 00:00:00'),
					(16, 7, 1, NULL, 4, 'Ricco Melvin III', '786-345-3309', 'APT 205', '2016-01-08 13:35:36', '0000-00-00 00:00:00'),
					(17, 4, 2, NULL, 4, 'William Nelson', '', '654', '2016-01-08 13:36:15', '0000-00-00 00:00:00'),
					(18, 7, 2, NULL, 3, 'Ronda Wendell', '(877) 776-6508', '103', '2016-01-08 13:36:23', '2016-01-02 14:39:25'),
					(19, 7, 2, NULL, 1, 'Wester Costell', '(305) 344-3487', '495', '2016-01-08 13:36:31', '2016-01-02 15:09:19'),
					(20, 6, 3, NULL, 5, 'Sisel Nornam', '305-345-5459', '904', '2016-01-08 23:23:23', '2016-01-07 20:18:40'),
					(21, 9, 3, NULL, 6, 'Timothy', '', '1102', '2016-01-08 23:23:38', '0000-00-00 00:00:00'),
					(22, 7, 4, NULL, 2, 'Adam and Cindie Lorrell', '305-334-0494', '234', '2016-01-08 23:24:14', '2016-01-02 08:47:42'),
					(23, 4, 5, NULL, 0, 'Fred Astaire', '(433) 443-4334', '233', '2016-01-08 23:24:47', '2016-01-01 17:54:46'),
					(26, 4, 5, NULL, 0, 'Marvin Castel', '(493) 343-3443', '256', '2016-01-08 23:25:02', '2016-01-01 18:04:11'),
					(27, 4, 6, 1, 0, 'Sammy Dolphirs', NULL, '443', '2016-01-08 23:25:29', '2016-01-01 18:05:40'),
					(28, 4, 6, NULL, 0, 'Remy Martin', NULL, '344', '2016-01-08 23:25:46', '2016-01-01 18:13:13'),
					(30, 7, 7, NULL, 0, 'Ralph Hater Jr', '(656) 556-3443', '333', '2016-01-08 23:26:12', '2016-01-05 06:01:16'),
					(32, 2, 8, NULL, 0, 'Remy Martin', '(456) 436-7887', 'APT 24', '2016-01-08 23:26:45', '2016-01-04 07:40:47'),
					(33, 12, 9, NULL, 0, 'Ralph Noder', '(566) 565-6556', '343', '2016-01-08 23:27:15', '2016-01-04 22:15:48'),
					(35, 8, 10, NULL, 0, 'Richard', '(887) 877-8876', '235', '2016-01-08 23:28:22', '2016-01-05 20:58:39'),
					(37, 8, 10, NULL, 0, 'Make Inactive', NULL, '234 NOT ACTIVE', '2016-01-08 23:28:30', '2016-01-05 22:30:06');
					";
					
		DB::table('addresses')->insert($seederQ);
		
    }
}
