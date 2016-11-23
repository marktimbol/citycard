<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = factory(App\Category::class)->create([
            'name'  => 'Food'
        ]);

        $category->subcategories()->save(factory(App\Subcategory::class)->create([
            'category_id'   => $category->id,
            'name'  => 'Buffet',
        ]));

        $category->subcategories()->save(factory(App\Subcategory::class)->create([
            'category_id'   => $category->id,
            'name'  => 'Fast Food',
        ]));

        $category = factory(App\Category::class)->create([
            'name'  => 'Beauty'
        ]);

        $category->subcategories()->save(factory(App\Subcategory::class)->create([
            'category_id'   => $category->id,
            'name'  => 'Hair Salons',
        ]));

        $category->subcategories()->save(factory(App\Subcategory::class)->create([
            'category_id'   => $category->id,
            'name'  => 'Moroccan Bath',
        ]));
    }
}
