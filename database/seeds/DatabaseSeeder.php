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
        $this->call(RoleTableSeeder::class);

        // Initial Admin User
        $admin = factory(App\Admin::class)->create([
            'name'  => 'Belle Faustino',
        	'email'	=> 'belle@faustino.com',
        	'password'	=> bcrypt('belle123')
        ]);

        $admin->assignRole('admin');

        $admin2 = factory(App\Admin::class)->create([
            'name'  => 'City Card Admin',
        	'email'	=> 'admin@citycard.me',
        	'password'	=> bcrypt('marktimbol')
        ]);

        $admin2->assignRole('admin');

        factory(App\User::class)->create([
            'name'  => 'City Card User',
            'email' => 'user@timbol.com',
            'password'  => bcrypt('marktimbol')
        ]);


        $this->call(CountriesTableSeeder::class);
        // $this->call(CategoriesTableSeeder::class);
        $this->call(SourcesTableSeeder::class);
    }
}
