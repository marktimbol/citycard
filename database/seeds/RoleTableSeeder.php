<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = factory(App\Role::class)->create([
        	'name'	=> 'admin',
        	'label'	=> 'Site Administrator'
        ]);

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_merchants',
        	'label'	=> 'Manage Merchants'
        ]);

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_countries',
        	'label'	=> 'Manage Countries'
        ]);

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_admins',
        	'label'	=> 'Manage Site Admins'
        ]); 

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_users',
        	'label'	=> 'Manage Registered Users'
        ]); 

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_categories',
        	'label'	=> 'Manage Categories'
        ]);  

		$permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_externals',
        	'label'	=> 'Manage External links'
        ]); 

        $permissions[] = factory(App\Permission::class)->create([
        	'name'	=> 'manage_roles',
        	'label'	=> 'Manage Roles'
        ]);

        foreach( $permissions as $permission )
        {
        	$adminRole->givePermissionTo($permission);
        }
    }
}
