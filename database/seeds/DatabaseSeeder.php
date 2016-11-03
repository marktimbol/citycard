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

        // We will start with 5 merchants
        $merchants = factory(App\Merchant::class, 5)->create();

        $excel = App::make('excel');

        $excel->load('merchants.xls', function($reader) {
            $merchants = $reader->all();
            foreach( $merchants as $merchant )
            {
                Merchant::create([
                    'name'  => $merchant->name,
                    'phone' => $merchant->phone,
                    'city'  => $merchant->city,
                    'country'   => $merchant->country,
                    'email' => $merchant->email,
                    'password'  => bcrypt($merchant->password)
                ]);
            }

            return 'Done';
        })->get();

        $merchants = Merchant::all();

        foreach( $merchants as $merchant )
        {
            // Each merchant has 5 outlets
        	$outlets = factory(App\Outlet::class, 5)->create([
        		'merchant_id'	=> $merchant->id
        	]);
            
            // Each merchant has 5 clerks
        	$clerks = factory(App\Clerk::class, 5)->create([
        		'merchant_id'	=> $merchant->id
        	]);

        	foreach( $outlets as $outlet )
        	{
                // In each Merchant's outlet, we have 5 clerks
                $outlet->clerks()->attach($clerks);
                
                // In each outlet, we have 5 promotions
                $promos = factory(App\Promo::class, 5)->create([
                    'merchant_id'   => $merchant->id
                ]);

                // We will attach the promos in outlet
                $outlet->promos()->attach($promos);
        	}
        }
    }
}
