<?php namespace Modules\Base\Database\Seeders\Access;

use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Class RoleTableSeeder
 */
class RoleTableSeeder extends Seeder
{
    public function run()
    {
        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        if (env('DB_CONNECTION') == 'mysql') {
            DB::table(config('base.roles_table'))->truncate();
        } elseif (env('DB_CONNECTION') == 'sqlite') {
            DB::statement('DELETE FROM ' . config('base.roles_table'));
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE ' . config('base.roles_table') . ' CASCADE');
        }

        //Create admin role, id of 1
        $role_model        = config('base.role');
        $super_admin             = new $role_model;
        $super_admin->name       = 'Super Admins';
        $super_admin->description = 'Access to all';
        $super_admin->all        = true;
        $super_admin->sort       = 1;
        $super_admin->created_at = Carbon::now();
        $super_admin->updated_at = Carbon::now();
        $super_admin->save();

        //id = 2
        $role_model       = config('base.role');
        $consumers             = new $role_model;
        $consumers->name       = 'General User';
        $consumers->description = 'General registered user';
        $consumers->sort       = 2;
        $consumers->created_at = Carbon::now();
        $consumers->updated_at = Carbon::now();
        $consumers->save();

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}