<?php namespace Modules\Base\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessTableSeeder extends Seeder
{
    public function run()
    {
        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $this->call(Access\UserTableSeeder::class);
        $this->call(Access\RoleTableSeeder::class);
        $this->call(Access\UserRoleSeeder::class);
        $this->call(Access\PermissionGroupTableSeeder::class);
        $this->call(Access\PermissionTableSeeder::class);
        $this->call(Access\PermissionDependencyTableSeeder::class);

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

    }
}