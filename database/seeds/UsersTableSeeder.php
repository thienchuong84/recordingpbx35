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
	        'email'    	=> 'admin@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '1702',
            'roles' => '1*, 51*, 57*',
            'is_active' => '1'
        ));        
    	
	    \App\User::create(array(
	        'name'     	=> 'Chuong Nguyen',
	        'email'    	=> 'chuong.nt@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '1702',
            'roles' => '1*, 51*, 57*',
            'is_active' => '1'
        ));
        
	    \App\User::create(array(
	        'name'     	=> 'Khanh Truong',
	        'email'    	=> 'khanh.tt@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '1700',
            'roles' => '1*, 57*',
            'is_active' => '1'
        ));     
        
	    \App\User::create(array(
	        'name'     	=> 'Mai Hong',
	        'email'    	=> 'mai.hong@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '57*',
            'is_active' => '1'
        ));
        
	    \App\User::create(array(
	        'name'     	=> 'Quynh Pham',
	        'email'    	=> 'quynh.ptt@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '57*',
            'is_active' => '1'
	    ));          
        
        \App\User::create(array(
	        'name'     	=> 'Sanh Vuong',
	        'email'    	=> 'sang.vuongcam@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '57*',
            'is_active' => '1'
        ));
        
	    \App\User::create(array(
	        'name'     	=> 'Phuong Ly',
	        'email'    	=> 'phuong.ly@pivotalvietnam.com',
            'password' 	=> Hash::make('123456'),
            'extension' => '',
            'roles' => '57*',
            'is_active' => '1'
	    ));          
    }
}
