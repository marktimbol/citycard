<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageOutletAlbumsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp()
	{
		parent::setUp();

		$this->actingAsClerk([
			'first_name'	=> 'Clerk'
		]);
	}

    public function test_an_authorized_clerk_can_view_all_the_outlet_albums()
    {
        $outlet = $this->createOutlet([
        	'name'	=> 'Starbucks'
        ]);

        $januaryPhotosAlbum = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'January Photos Album'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $januaryPhotosAlbum->id,
        	'url'	=> 'http://google.com'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $januaryPhotosAlbum->id,
        	'url'	=> 'http://google.com'
        ]);

        $februaryPhotosAlbum = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'February Photos Album'
        ]);     

        $this->createAlbumPhoto([
        	'album_id'	=> $februaryPhotosAlbum->id,
        	'url'	=> 'http://google.com'
        ]);           

        $request = $this->get('/api/clerk/outlets/'.$outlet->id.'/albums');

        $this->seeJson([
        	'title'	=> 'January Photos Album',
        	'photos_count'	=> 2,
        ]);

        $this->seeJson([
        	'title'	=> 'February Photos Album',
        	'photos_count'	=> 1,
        ]);
    }

    public function test_an_authorized_clerk_can_view_an_outlet_album_photos()
    {
        $outlet = $this->createOutlet([
        	'name'	=> 'Starbucks'
        ]);

        $album = $this->createAlbum([
        	'outlet_id'	=> $outlet->id,
        	'title'	=> 'January Photos Album'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $album->id,
        	'url'	=> 'http://facebook.com'
        ]);

        $this->createAlbumPhoto([
        	'album_id'	=> $album->id,
        	'url'	=> 'http://google.com'
        ]);

        $request = $this->get('/api/clerk/outlets/'.$outlet->id.'/albums/'.$album->id);

        $this->seeJson([
        	'photos_count'	=> 2,
        ]);
        $this->seeJson([
        	'url'	=> 'http://facebook.com',
        ]);
        $this->seeJson([
        	'url'	=> 'http://google.com',
        ]);
    }

    public function test_an_authorized_clerk_can_create_an_outlet_album()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Starbucks'
        ]);

        $request = $this->post('/api/clerk/outlets/'.$outlet->id.'/albums', [
            'title' => 'January Photos'
        ]);

        $this->seeInDatabase('albums', [
            'outlet_id' => $outlet->id,
            'title' => 'January Photos'
        ]);

        $this->seeJson([
            'success'   => true,
        ]);
    }

    public function test_an_authorized_clerk_can_edit_an_outlet_album_title()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Starbucks'
        ]);

        $album = $this->createAlbum([
            'outlet_id' => $outlet->id,
            'title' => 'Januaryy Photos'
        ]);

        $this->seeInDatabase('albums', [
            'outlet_id' => $outlet->id,
            'title' => 'Januaryy Photos'
        ]);

        $request = $this->put('/api/clerk/outlets/'.$outlet->id.'/albums/'.$album->id, [
            'title' => 'January Photos'
        ]);

        $this->seeInDatabase('albums', [
            'outlet_id' => $outlet->id,
            'title' => 'January Photos'
        ]);

        $this->seeJson([
            'success'   => true,
        ]);
    }

    public function test_an_authorized_clerk_can_delete_an_outlet_album()
    {
        $outlet = $this->createOutlet([
            'name'  => 'Starbucks'
        ]);

        $album = $this->createAlbum([
            'outlet_id' => $outlet->id,
            'title' => 'January Photos'
        ]);

        $request = $this->delete('/api/clerk/outlets/'.$outlet->id.'/albums/'.$album->id);

        $this->dontSeeInDatabase('albums', [
            'id'    => $album->id,
            'outlet_id' => $outlet->id,
            'title' => 'January Photos'
        ]);

        $this->seeJson([
            'success'   => true,
        ]);
    }    
}
