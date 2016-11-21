<?php

use Illuminate\Database\Seeder;

class SourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Source::class)->create([
            'name'  => 'Cobone',
        ]);

        factory(App\Source::class)->create([
            'name'  => 'Talabat',
        ]);

        factory(App\Source::class)->create([
            'name'  => 'Groupon',
        ]);
    }
}
