<?php

use Illuminate\Database\Migrations\Migration;

class SetupSluggableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('base.sluggable_table'), function ($table) {
            $table->increments('id');
            $table->string('property_code');
            $table->string('slug');
            $table->integer('sluggable_id');
            $table->string('sluggable_type');

            $table->index(['property_code', 'sluggable_type', 'sluggable_id'], 'sluggable_search_idx');

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create(config('base.sluggable_table').'_uuid', function ($table) {
            $table->increments('id');
            $table->string('property_code');
            $table->string('slug');
            $table->uuid('sluggable_id');
            $table->string('sluggable_type');

            $table->index(['property_code', 'sluggable_type', 'sluggable_id'], 'sluggable_search_idx');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {


        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        /**
         * Drop tables
         */
        Schema::drop(config('base.sluggable_table'));
        Schema::drop(config('base.sluggable_table').'_uuid');

        if (env('DB_CONNECTION') == 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
