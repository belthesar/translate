<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		Role::firstOrCreate( [
			'name' => 'Root'
		] );

		User::find( 101 )->roles()->attach( 1 );
	}
}
