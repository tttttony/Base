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
            DB::table(config('access.roles_table'))->truncate();
        } elseif (env('DB_CONNECTION') == 'sqlite') {
            DB::statement('DELETE FROM ' . config('access.roles_table'));
        } else {
            //For PostgreSQL or anything else
            DB::statement('TRUNCATE TABLE ' . config('access.roles_table') . ' CASCADE');
        }

        //Create admin role, id of 1
        $role_model        = config('access.role');
        $super_admin             = new $role_model;
        $super_admin->name       = 'Super Admins';
        $super_admin->description = 'All Access Babylock Admins';
        $super_admin->all        = true;
        $super_admin->sort       = 1;
        $super_admin->created_at = Carbon::now();
        $super_admin->updated_at = Carbon::now();
        $super_admin->save();

//        //id = 2
//        $role_model       = config('access.role');
//        $admin             = new $role_model;
//        $admin->name       = 'Admins';
//        $admin->description = 'Group for overall administrators';
//        $admin->sort       = 2;
//        $admin->created_at = Carbon::now();
//        $admin->updated_at = Carbon::now();
//        $admin->save();
//
//        //id = 3
//        $role_model       = config('access.role');
//        $educator             = new $role_model;
//        $educator->name       = 'Educators';
//        $educator->description = 'Consumer Educator group';
//        $educator->sort       = 3;
//        $educator->created_at = Carbon::now();
//        $educator->updated_at = Carbon::now();
//        $educator->save();
//
//        //id = 4
//        $role_model       = config('access.role');
//        $trainers             = new $role_model;
//        $trainers->name       = 'Trainers';
//        $trainers->description = 'Retailer Trainer group';
//        $trainers->sort       = 4;
//        $trainers->created_at = Carbon::now();
//        $trainers->updated_at = Carbon::now();
//        $trainers->save();
//
//        //id = 5
//        $role_model       = config('access.role');
//        $rsm             = new $role_model;
//        $rsm->name       = 'RSM';
//        $rsm->description = 'Regional Sales Managers';
//        $rsm->sort       = 5;
//        $rsm->created_at = Carbon::now();
//        $rsm->updated_at = Carbon::now();
//        $rsm->save();
//
//        //id = 6
//        $role_model       = config('access.role');
//        $cs             = new $role_model;
//        $cs->name       = 'Customer Service';
//        $cs->description = 'Customer Service rep account';
//        $cs->sort       = 6;
//        $cs->created_at = Carbon::now();
//        $cs->updated_at = Carbon::now();
//        $cs->save();
//
//        //id = 7
//        $role_model       = config('access.role');
//        $guest             = new $role_model;
//        $guest->name       = 'Guest';
//        $guest->description = 'Guest Hosts or Authors';
//        $guest->sort       = 7;
//        $guest->created_at = Carbon::now();
//        $guest->updated_at = Carbon::now();
//        $guest->save();
//
//        //id = 8
//        $role_model       = config('access.role');
//        $customer             = new $role_model;
//        $customer->name       = 'Customer';
//        $customer->description = 'Customers without Logins';
//        $customer->sort       = 8;
//        $customer->created_at = Carbon::now();
//        $customer->updated_at = Carbon::now();
//        $customer->save();
//
//        //id = 9
//        $role_model       = config('access.role');
//        $employee             = new $role_model;
//        $employee->name       = 'Employee';
//        $employee->description = 'Basic Admin viewing privileges with pending submissions';
//        $employee->sort       = 9;
//        $employee->created_at = Carbon::now();
//        $employee->updated_at = Carbon::now();
//        $employee->save();
//
//        //id = 10
//        $role_model       = config('access.role');
//        $owners             = new $role_model;
//        $owners->name       = 'Owners';
//        $owners->description = 'Owner group for retailers';
//        $owners->sort       = 10;
//        $owners->created_at = Carbon::now();
//        $owners->updated_at = Carbon::now();
//        $owners->save();
//
//        //id = 11
//        $role_model       = config('access.role');
//        $manager             = new $role_model;
//        $manager->name       = 'Manager-level Access';
//        $manager->description = 'Recommended for those who manage employees, consumers, services, promotions and training at one (or more) store location(s).';
//        $manager->sort       = 11;
//        $manager->created_at = Carbon::now();
//        $manager->updated_at = Carbon::now();
//        $manager->save();
//
//        //id = 12
//        $role_model       = config('access.role');
//        $technician             = new $role_model;
//        $technician->name       = 'Technician-level Access';
//        $technician->description = 'Recommended for those who need to train, perform, and submit services on Baby Lock machines.';
//        $technician->sort       = 12;
//        $technician->created_at = Carbon::now();
//        $technician->updated_at = Carbon::now();
//        $technician->save();
//
//        //id = 13
//        $role_model       = config('access.role');
//        $employeelevel             = new $role_model;
//        $employeelevel->name       = 'Employee-level Access';
//        $employeelevel->description = 'Recommended for those who want to train, advertise, and sell Baby Lock products.';
//        $employeelevel->sort       = 13;
//        $employeelevel->created_at = Carbon::now();
//        $employeelevel->updated_at = Carbon::now();
//        $employeelevel->save();
//
//        //id = 14
//        $role_model       = config('access.role');
//        $thirdparty             = new $role_model;
//        $thirdparty->name       = '3rd-party Access';
//        $thirdparty->description = 'Recommended for those who need access to Baby Lock promotional resources only.';
//        $thirdparty->sort       = 14;
//        $thirdparty->created_at = Carbon::now();
//        $thirdparty->updated_at = Carbon::now();
//        $thirdparty->save();

        //id = 2
        $role_model       = config('access.role');
        $consumers             = new $role_model;
        $consumers->name       = 'Consumers';
        $consumers->description = 'End users with site logins';
        $consumers->sort       = 2;
        $consumers->created_at = Carbon::now();
        $consumers->updated_at = Carbon::now();
        $consumers->save();

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}