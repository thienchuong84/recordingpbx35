<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

	    \App\User::create(array(
	        'name'     	=> 'Admin',
	        'email'    	=> 'admin@duongnguyenimc.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '1*',
            'is_active' => '1'
        ));        
    	
	    \App\User::create(array(
	        'name'     	=> 'Ai Duong',
	        'email'    	=> 'aiduong@duongnguyenimc.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '1*',
            'is_active' => '1'
        ));
        
	    \App\User::create(array(
	        'name'     	=> 'Anh Nguyen',
	        'email'    	=> 'hoanganh6298@yahoo.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '1*',
            'is_active' => '1'
        ));     
        
          
    }
}
