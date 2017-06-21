<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupAccessTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('base.users_table'), function ($table) {
            $table->tinyInteger('status')->after('password')->default(1)->unsigned();
            $table->string('username')->after('id')->unique()->nullable();
        });

        Schema::create(config('base.roles_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->text('description');
            $table->boolean('all')->default(false);
            $table->smallInteger('sort')->default(0)->unsigned();
            $table->timestamps();

            /**
             * Add Foreign/Unique/Index
             */
            $table->unique('name');
        });

        Schema::create(config('base.assigned_roles_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();

            /**
             * Add Foreign/Unique/Index
             */

            $table->foreign('role_id')
                ->references('id')
                ->on(config('base.roles_table'))
                ->onDelete('cascade');
        });

        Schema::create(config('base.permissions_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('group_id')->nullable()->unsigned();
            $table->string('name');
            $table->string('display_name');
            $table->boolean('system')->default(false);
            $table->smallInteger('sort')->default(0)->unsigned();
            $table->timestamps();

            /**
             * Add Foreign/Unique/Index
             */
            $table->unique('name');
        });

        Schema::create(config('base.permission_group_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('parent_id')->nullable();
            $table->string('name');
            $table->smallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create(config('base.permission_role_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            /**
             * Add Foreign/Unique/Index
             */
            $table->foreign('permission_id')
                ->references('id')
                ->on(config('base.permissions_table'))
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on(config('base.roles_table'))
                ->onDelete('cascade');
        });

        Schema::create(config('base.permission_dependencies_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('dependency_id')->unsigned();
            $table->timestamps();

            /**
             * Add Foreign/Unique/Index
             */
            $table->foreign('permission_id')
                ->references('id')
                ->on(config('base.permissions_table'))
                ->onDelete('cascade');

            $table->foreign('dependency_id')
                ->references('id')
                ->on(config('base.permissions_table'))
                ->onDelete('cascade');
        });

        Schema::create(config('base.permission_user_table'), function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->integer('user_id')->unsigned();

            /**
             * Add Foreign/Unique/Index
             */
            $table->foreign('permission_id')
                ->references('id')
                ->on(config('base.permissions_table'))
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on(config('base.users_table'))
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*
        Schema::table(config('base.users_table'), function (Blueprint $table) {
            $table->dropColumn('status');
        });

        /**
         * Remove Foreign/Unique/Index
         /
        Schema::table(config('base.roles_table'), function (Blueprint $table) {
            $table->dropUnique(config('base.roles_table') . '_name_unique');
        });

        Schema::table(config('base.assigned_roles_table'), function (Blueprint $table) {
            $table->dropForeign(config('base.assigned_roles_table') . '_user_id_foreign');
            $table->dropForeign(config('base.assigned_roles_table') . '_role_id_foreign');
        });

        Schema::table(config('base.permissions_table'), function (Blueprint $table) {
            $table->dropUnique(config('base.permissions_table') . '_name_unique');
        });

        Schema::table(config('base.permission_role_table'), function (Blueprint $table) {
            $table->dropForeign(config('base.permission_role_table') . '_permission_id_foreign');
            $table->dropForeign(config('base.permission_role_table') . '_role_id_foreign');
        });

        Schema::table(config('base.permission_user_table'), function (Blueprint $table) {
            $table->dropForeign(config('base.permission_user_table') . '_permission_id_foreign');
            $table->dropForeign(config('base.permission_user_table') . '_user_id_foreign');
        });

        Schema::table(config('base.permission_dependencies_table'), function (Blueprint $table) {
            $table->dropForeign('permission_dependencies_permission_id_foreign');
            $table->dropForeign('permission_dependencies_dependency_id_foreign');
        });
        */

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        /**
         * Drop tables
         */
        Schema::drop(config('base.assigned_roles_table'));
        Schema::drop(config('base.permission_role_table'));
        Schema::drop(config('base.permission_user_table'));
        Schema::drop(config('base.permission_group_table'));
        Schema::drop(config('base.roles_table'));
        Schema::drop(config('base.permissions_table'));
        Schema::drop(config('base.permission_dependencies_table'));

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
