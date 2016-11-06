<?php

use Illuminate\Database\Seeder;

use App\Merchant;
use Maatwebsite\Excel\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Initial Admin User
        factory(App\Admin::class)->create([
        	'email'	=> 'mark@timbol.com',
        	'password'	=> bcrypt('marktimbol')
        ]);

        // $this->call(MerchantsTableSeeder::class);
    }
}
