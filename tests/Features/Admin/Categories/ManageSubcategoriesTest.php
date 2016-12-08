<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageSubcategoriesTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_view_all_the_available_subcategories_in_category()
    {
        $category = $this->createCategory([
            'name'  => 'Food'
        ]);

        $subcategory = $this->createSubcategory([
            'category_id'   => $category->id,
            'name'  => 'Buffet'
        ]);

        $category->subcategories()->save($subcategory);

        $url = sprintf(adminPath().'/dashboard/categories/%s/subcategories', $category->id);
        $this->visit($url)
            ->see('Buffet');
    }

    public function test_an_admin_can_create_a_new_subcategory_for_a_category()
    {
        $category = $this->createCategory([
            'name'  => 'Food'
        ]);

        $url = sprintf(adminPath() . '/dashboard/categories/%s/subcategories', $category->id);
        $this->visit($url)
            ->type('Brunch', 'name')
            ->press('Save')

            ->seeInDatabase('subcategories', [
                'category_id'   => $category->id,
                'name'  => 'Brunch'
            ]);
    }

    public function test_an_admin_can_delete_a_category()
    {
        $category = $this->createCategory([
            'name'  => 'Food'
        ]);

        $url = sprintf(adminPath() . '/dashboard/categories/%s', $category->id);
        $this->visit($url)
            ->press('Delete')

            ->dontSeeInDatabase('categories', [
                'id'    => $category->id,
            ]);
    }
}
