<?php

namespace App;

// include('IP2Location.php');

trait Filterable
{
    protected $city;
    protected $areas = [];
    protected $categories = [];
    // protected $subcategories = [];

    public static function filterBy($request)
    {
        // $this->byCity($city)
        //     ->andByAreas($areas)
        //     ->andByCategory($category)
        //     ->andBySubcategories($subcategories)
        //     ->get();

        $city = ! empty($request['city_id']) ? $request['city_id'] : 0;
        $areas = ! empty($request['area_ids']) ? $request['area_ids'] : [];
        $categories = ! empty($request['categories']) ? $request['categories'] : [];

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

        if( count($categories) > 0 )
        {
            $posts = $posts->filter(function($post, $key) use ($categories) {
                return in_array($post->category->id, $categories);
            });

            return Post::with(['category', 'outlets', 'merchant', 'photos', 'sources'])
                    ->latest()
                    ->whereIn('id', $posts->pluck('id'));            
        }

        $posts->load('outlets.areas.city', 'photos', 'sources');

        $posts = $posts->filter(function($post, $key) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    if( count($this->areas) > 0 ) {
                        return in_array($area->id, $this->areas);
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
