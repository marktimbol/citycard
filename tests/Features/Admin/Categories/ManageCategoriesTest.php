<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ManageCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->adminSignIn();
    }

    public function test_an_admin_can_view_all_the_available_categories()
    {
        $category = $this->createCategory([
            'name'  => 'Food'
        ]);

        $this->visit(adminPath() . '/dashboard/categories')
            ->see('Food');
    }

    public function test_an_admin_can_create_a_new_category()
    {
        $this->visit(adminPath() . '/dashboard/categories')
            ->type('Food', 'name')
            ->press('Save')

            ->seeInDatabase('categories', [
                'name'  => 'Food'
            ]);
    }

    public function test_an_admin_can_delete_a_category()
    {
        $category = $this->createCategory([
            'name'  => 'Food'
        ]);

        $url = sprintf('%s/dashboard/categories/%s', adminPath(), $category->id);

        $this->delete($url);
        $this->dontSeeInDatabase('categories', [
            'id'    => $category->id,
        ]);
    }
}
