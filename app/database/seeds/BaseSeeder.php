<?php

class BaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		$user = array(
			"firstName"=> "ניקולאי",
			"lastName"=> "לוי",
		    "username"=> "nick",
		    "password"=> 123,
		    "email"=> "nick@webt.co.il",
		    "states_id"=>2,
			);
		User::create($user);
		$user = array(
			"firstName"=> "ברברה",
			"lastName"=> "וולצ'ר",
		    "username"=> "bar",
		    "password"=> 123,
		    "email"=> "barbara@webt.co.il",
		    "states_id"=>2,
			);
		User::create($user);
	}

}