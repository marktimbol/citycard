<?php

use App\Merchant;
use Illuminate\Database\Seeder;

class MerchantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
            // Each merchant has 2 outlets
        	$outlets = factory(App\Outlet::class, 2)->create([
        		'merchant_id'	=> $merchant->id
        	]);
            
            // Each merchant has 2 clerks
        	$clerks = factory(App\Clerk::class, 2)->create([
        		'merchant_id'	=> $merchant->id
        	]);

        	foreach( $outlets as $outlet )
        	{
                // In each Merchant's outlet, we have 2 clerks
                $outlet->clerks()->attach($clerks);

                // In each outlet, we have 2 posts
                $posts = factory(App\Post::class, 2)->create([
                    'merchant_id'   => $merchant->id
                ]);

                // We will attach the posts in outlet
                $outlet->posts()->attach($posts);
        	}
        }
    }
}
