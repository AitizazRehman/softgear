<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public $data = [
      'edit_user',
      'add_user',
      'delete_user',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $id_admin = DB::table('roles')->insertGetId([
          'name'  => 'admin',
          'guard_name' => 'api'
      ]);
      DB::table('roles')->insertGetId([
          'name'  => 'user',
          'guard_name' => 'api'
      ]);

      /////////////// assign permissions to super user
      foreach ($this->data as $key => $value) {
        $id = DB::table('permissions')->insertGetId([
            'name'  => $value,
            'guard_name' => 'api'
        ]);
        DB::table('role_has_permissions')->insertGetId([
            'permission_id'  => $id,
            'role_id' => $id_admin
          ]);
        }
        /////////////////// asign super user to admin user
       DB::table('model_has_roles')->insertGetId([
         'role_id' => $id_admin,
         'model_id' => 1,
         'model_type' => 'App\User'
       ]);
    }
}
