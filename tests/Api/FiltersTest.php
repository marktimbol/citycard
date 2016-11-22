<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FiltersTest extends TestCase
{
    use DatabaseMigrations;

    public function test_it_filters_posts_by_city()
    {
        // $abuDhabi = $this->createCity([
        //     'name'  => 'Abu Dhabi'
        // ]);

        // City
        $dubai = $this->createCity([
            'name'  => 'Dubai'
        ]);

        // Area
        $alRiggaArea = $this->createArea([
            'city_id'   => $dubai->id,
            'name'  => 'Al Rigga'
        ]);

        // Merchant
        $zara = $this->createMerchant([
            'name'  => 'Zara'
        ]);

        // Zara in Al Rigga
        $zaraAlRigga = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Al Rigga'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $alRiggaArea->outlets()->attach($zaraAlRigga);

        $post = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara Offer in Dubai'
        ]);
        $zaraAlRigga->posts()->attach($post);


        // Abu Dhabi
        $abuDhabi = $this->createCity([
            'name'  => 'Abu Dhabi'
        ]);

        // Area
        $abuDhabiArea = $this->createArea([
            'city_id'   => $abuDhabi->id,
            'name'  => 'Abu Dhabi Area'
        ]);

        // Zara in Al Rigga
        $zaraAbuDhabi = $this->createOutlet([
            'merchant_id'   => $zara->id,
            'name'  => 'Zara - Abu Dhabi'
        ]);

        // Add Zara - Al Rigga branch to Al Rigga area
        $abuDhabiArea->outlets()->attach($zaraAbuDhabi);

        $post2 = $this->createPost([
            'merchant_id'   => $zara->id,
            'title' => 'Zara New Offer in Abu Zabbi'
        ]);
        $zaraAbuDhabi->posts()->attach($post2);

        $endpoint = sprintf('/api/filters/?city_id=%s', $dubai->id);
        $response = $this->get($endpoint)
            ->seeJson([
                'title' => 'Zara Offer in Dubai'
            ]);
    }
}
