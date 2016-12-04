<?php

namespace App;

// include('IP2Location.php');

trait Filterable
{
    protected $city;
    protected $areas = [];
    protected $categories = [];

    public static function filterBy($request)
    {
        // $this->byCity($city)
        //     ->andByAreas($areas)
        //     ->andByCategories($categories)
        //     ->get();

        $city = $request->city;
        $areas = $request->areas;
        $categories = $request->categories;

        return (new static)
            ->byCity($city)
            ->andByAreas($areas)
            ->andByCategories($categories)
            ->get();
    }

    protected function byCity($city)
    {
        $this->city = $city;
        return $this;
    }

    protected function andByAreas($areas)
    {
        $this->areas = $areas;
        return $this;
    }

    protected function andByCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    protected function get()
    {
        $posts = Post::with('category')->latest()->get();
        $categories = $this->categories;

        if( $categories != '' )
        {
            $categories = explode(',', $categories);
            $posts = $posts->filter(function($post, $key) use ($categories) {
                return in_array($post->category->id, $categories);
            });

            return Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])
                    ->latest()
                    ->whereIn('id', $posts->pluck('id'));            
        }

        $posts->load('outlets.areas.city');

        $posts = $posts->filter(function($post, $key) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    if( $this->areas != '' ) {
                        $areas = explode(',', $this->areas);
                        return in_array($area->id, $areas);
                    }
                    return $this->city == $area->city->id;
                }
            }
        });

        return Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])
                ->latest()
                ->whereIn('id', $posts->pluck('id'));
    }
}
