<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminShouldBeAbleToLoginTest extends TestCase
{
	use DatabaseMigrations;

    public function test_an_admin_should_be_able_to_login_with_correct_credentials()
    {
    	$admin = $this->createAdmin([
			'name'	=> 'Admin',
			'email'	=> 'email@admin.com',
			'password'	=> bcrypt('secret')
    	]);

    	$this->visit('/admin/login')
    		->type('email@admin.com', 'email')
    		->type('secret', 'password')
    		->press('Login')

    		->seePageIs('/admin/dashboard');
    }

    public function test_an_admin_should_not_be_able_to_login_with_incorrect_credentials()
    {
        $admin = $this->createAdmin([
            'name'  => 'Admin',
            'email' => 'email@admin.com',
            'password'  => 'secret'         
        ]);

    	$this->visit('/admin/login')
            ->type('email@admin.com', 'email')
            ->type('secrets', 'password')
    		->press('Login')

    		->seePageIs('/admin/login');
    }
}
