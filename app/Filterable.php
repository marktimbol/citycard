<?php

namespace App;

trait Filterable
{
    protected $city;
    protected $areas = [];
    protected $categories = [];
    // protected $subcategories = [];

    public static function filterBy($request)
    {
        // dd($request);

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
            return $posts->filter(function($post, $key) use ($categories) {
                return in_array($post->category->id, $categories);
            });
        }

        $posts->load('outlets.areas.city', 'photos', 'sources');

        $city = $this->city;
        $areas = $this->areas;

        return $posts->filter(function($post, $key) use ($city, $areas) {
            foreach( $post->outlets as $outlet ) {
                foreach( $outlet->areas as $area ) {
                    if( count($areas) > 0 ) {
                        return in_array($area->id, $areas);
                    }
                    return $city == $area->city->id;
                }
            }
        })->all();
    }

    // protected function byCity($city)
    // {
    //     $posts = Post::latest()->get();
    //     $posts->load('outlets.areas.city', 'photos', 'sources');
    //
    //     return $posts->filter(function($post, $key) use ($city) {
    //         foreach( $post->outlets as $outlet ) {
    //             foreach( $outlet->areas as $area ) {
    //                 return $city->id == $area->city->id;
    //             }
    //         }
    //     })->all();
    // }
    //
    // protected function byArea($selectedArea)
    // {
    //     $posts = Post::has('outlets')->latest()->get();
    //     $posts->load('outlets.areas', 'photos', 'sources');
    //
    //     return $posts->filter(function($post, $key) use ($selectedArea) {
    //         foreach( $post->outlets as $outlet ) {
    //             foreach( $outlet->areas as $area ) {
    //                 return $selectedArea->id == $area->id;
    //             }
    //         }
    //     })->all();
    // }
    //
    // protected function byAreas($area_ids)
    // {
    //     $posts = Post::has('outlets')->latest()->get();
    //     $posts->load('outlets.areas', 'photos', 'sources');
    //
    //     return $posts->filter(function($post, $key) use ($area_ids) {
    //         foreach( $post->outlets as $outlet ) {
    //             foreach( $outlet->areas as $area ) {
    //                 return in_array($area->id, $area_ids);
    //             }
    //         }
    //     })->all();
    // }
}
