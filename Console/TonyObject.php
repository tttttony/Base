<?php
namespace Modules\Base\Console;

use DB;
use Illuminate\Console\Command;
use Storage;

class TonyObject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tony:object {module} {object}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make an object for modules';

    protected $module = '';
    protected $module_plural = '';
    protected $object = '';
    protected $object_plural = '';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->module = ucwords($this->argument('module'));
        $this->module_plural = str_plural($this->module);
        $this->object = ucwords($this->argument('object'));
        $this->object_plural = str_plural($this->object);

        //TODO: If the module is new, build the base module files too.
	    $this->checkModule();

        //TODO: support for 'ables' like taggable relationships
        //TODO: make the ability to pass an array of attributes for the objects and change the database, entities, and form file to reflex

        //Database
        $this->put($this->module . '/Database/Migrations/' . date('Y_m_d', time()) . '_000000_create_' . strtolower($this->object) . '_table.php', $this->getCreateTableFile());
        $this->put($this->module . '/Database/Seeders/' . $this->object . 'PermissionSeeder.php', $this->getPermissionSeeder());
        //Entities
        $this->put($this->module . '/Entities/' . $this->object . '.php', $this->getEntity());
        $this->put($this->module . '/Entities/Traits/Relationships/' . $this->object . 'Relationships.php', $this->getRelationships());
        //Controller
        $this->put($this->module . '/Http/Controllers/' . $this->object_plural . 'Controller.php', $this->getController());
        //Requests
        $this->put($this->module . '/Http/Requests/' . $this->object_plural . '/Create'.$this->object.'Request.php', $this->getRequest('create'));
        $this->put($this->module . '/Http/Requests/' . $this->object_plural . '/Delete'.$this->object.'Request.php', $this->getRequest('delete'));
        $this->put($this->module . '/Http/Requests/' . $this->object_plural . '/Edit'.$this->object.'Request.php', $this->getRequest('edit'));
        $this->put($this->module . '/Http/Requests/' . $this->object_plural . '/Store'.$this->object.'Request.php', $this->getRequest('store'));
        $this->put($this->module . '/Http/Requests/' . $this->object_plural . '/Update'.$this->object.'Request.php', $this->getRequest('update'));
        //Repositories
        $this->put($this->module . '/Repositories/' . $this->object_plural . 'Repository.php', $this->getRepository());
        $this->put($this->module . '/Repositories/Eloquent/Eloquent' . $this->object_plural . 'Repository.php', $this->getEloquentRepository());

        //Resources
        $this->put($this->module . '/Resources/views/includes/' . strtolower($this->object_plural) . '-sidebar.blade.php', $this->getSidebarBlade());
        $this->put($this->module . '/Resources/views/' . $this->object_plural . '/create.blade.php', $this->getCreateBlade());
        $this->put($this->module . '/Resources/views/' . $this->object_plural . '/edit.blade.php', $this->getEditBlade());
        $this->put($this->module . '/Resources/views/' . $this->object_plural . '/form.blade.php', $this->getFormBlade());
	    $this->put($this->module . '/Resources/views/' . $this->object_plural . '/index.blade.php', $this->getIndexBlade());
	    $this->put($this->module . '/Resources/lang/en/' . strtolower($this->object) . '.php', $this->getLanguageFile());

	    $this->manualCodeChanges();

    }


	public function checkModule()
	{
		$disk = Storage::disk('modules');

		if(!$disk->has($this->module)) {
			$this->call('tony:module', ['module' => $this->module]);
		}
	}
	
	public function put($file, $content)
	{
		$disk = Storage::disk('modules');

		if($disk->has($file)) {
			$this->error($file."      !! File already exists, not overwriting");
			return false;
		}

		Storage::disk('modules')->put($file, $content);
		$this->info($file." created.");
	}

	public function manualCodeChanges()
	{
		$lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);
        
		//Output lines of code that need to be added manually to other existing files.
		//Route
		$this->error("\n\n!!!! Don't forget to manually add the following lines: ");

		$this->line("\n\tHttp\\routes.php");
		$this->info(<<<CODE
	Route::resource('{$lower_object_plural}', '{$this->object_plural}Controller', ['as' => 'admin']);
CODE
		);

		//Seeder link
		$this->line("\n\tDatabase\\Seeders\\".$this->module."PermissionSeeder.php");
		$this->info(<<<CODE
	\$this->call({$this->object}PermissionSeeder::class);
CODE
		);

		//binding in service provider
		$this->line("\n\tProviders\\".$this->module."ServiceProvider.php");
		$this->info(<<<CODE
	\$this->app->bind(
		\Modules\\{$this->module}\Repositories\\{$this->object_plural}Repository::class,
		function () {
			\$repository = new \Modules\\{$this->module}\Repositories\Eloquent\Eloquent{$this->object_plural}Repository(new \Modules\\{$this->module}\Entities\\{$this->object}());
			return \$repository;
		}
	);
CODE
		);

		//put sidebar in.
		$this->line("\n\tResources\\views\\includes\\sidebar.blade.php");
		$this->info(<<<CODE
	@include('{$lower_module}::includes.{$lower_object_plural}-sidebar')
CODE
		);
		$this->line("\n");
	}

    public function getCreateTableFile()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<EOD
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{$this->object}Table extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{$lower_module}__{$lower_object}', function(Blueprint \$table)
        {
            \$table->increments('id');
            \$table->string('name');

            \$table->softDeletes();
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('{$lower_module}__{$lower_object}');
    }

}
EOD;

    }

    public function getPermissionSeeder()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
<?php namespace Modules\\{$this->module}\Database\Seeders;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class {$this->object}PermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    Model::unguard();

        /**
         * {$this->object_plural}
         */
        \$permission_model = config('access.permission');
        \$view_management = \$permission_model::where('name', '{$lower_module}.view-management')->first();
        \$parent_group_id = \$view_management->group_id;

	    \$group_model = config('access.group');
	    \${$lower_object}_group = new \$group_model;
	    \${$lower_object}_group->name = '{$this->module}';
	    \${$lower_object}_group->sort = 1;
	    \${$lower_object}_group->parent_id = \$parent_group_id;
	    \${$lower_object}_group->created_at = Carbon::now();
	    \${$lower_object}_group->updated_at = Carbon::now();
	    \${$lower_object}_group->save();

        \$permission_model = config('access.permission');
        \$view_{$lower_object_plural}_management = new \$permission_model;
        \$view_{$lower_object_plural}_management->name = '{$lower_module}.{$lower_object}.view-management';
        \$view_{$lower_object_plural}_management->display_name = 'View {$this->object_plural} Management';
        \$view_{$lower_object_plural}_management->system = true;
        \$view_{$lower_object_plural}_management->group_id = \$parent_group_id;
        \$view_{$lower_object_plural}_management->sort = 5;
        \$view_{$lower_object_plural}_management->created_at = Carbon::now();
        \$view_{$lower_object_plural}_management->updated_at = Carbon::now();
        \$view_{$lower_object_plural}_management->save();
        DB::table(config('access.permission_dependencies_table'))->insert([
            'permission_id' => \$view_{$lower_object_plural}_management->id,
            'dependency_id' => \$view_management->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        {
            \$permission_model = config('access.permission');
            \$create_{$lower_object_plural} = new \$permission_model;
            \$create_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.create';
            \$create_{$lower_object_plural}->display_name = 'Create {$this->object_plural}';
            \$create_{$lower_object_plural}->system = true;
            \$create_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$create_{$lower_object_plural}->sort = 5;
            \$create_{$lower_object_plural}->created_at = Carbon::now();
            \$create_{$lower_object_plural}->updated_at = Carbon::now();
            \$create_{$lower_object_plural}->save();
            DB::table(config('access.permission_dependencies_table'))->insert([
                'permission_id' => \$create_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('access.permission');
            \$edit_{$lower_object_plural} = new \$permission_model;
            \$edit_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.edit';
            \$edit_{$lower_object_plural}->display_name = 'Edit {$this->object_plural}';
            \$edit_{$lower_object_plural}->system = true;
            \$edit_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$edit_{$lower_object_plural}->sort = 5;
            \$edit_{$lower_object_plural}->created_at = Carbon::now();
            \$edit_{$lower_object_plural}->updated_at = Carbon::now();
            \$edit_{$lower_object_plural}->save();
            DB::table(config('access.permission_dependencies_table'))->insert([
                'permission_id' => \$edit_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('access.permission');
            \$delete_{$lower_object_plural} = new \$permission_model;
            \$delete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.delete';
            \$delete_{$lower_object_plural}->display_name = 'Delete {$this->object_plural}';
            \$delete_{$lower_object_plural}->system = true;
            \$delete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$delete_{$lower_object_plural}->sort = 5;
            \$delete_{$lower_object_plural}->created_at = Carbon::now();
            \$delete_{$lower_object_plural}->updated_at = Carbon::now();
            \$delete_{$lower_object_plural}->save();
            DB::table(config('access.permission_dependencies_table'))->insert([
                'permission_id' => \$delete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('access.permission');
            \$undelete_{$lower_object_plural} = new \$permission_model;
            \$undelete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.undelete';
            \$undelete_{$lower_object_plural}->display_name = 'Restore {$this->object_plural}';
            \$undelete_{$lower_object_plural}->system = true;
            \$undelete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$undelete_{$lower_object_plural}->sort = 13;
            \$undelete_{$lower_object_plural}->created_at = Carbon::now();
            \$undelete_{$lower_object_plural}->updated_at = Carbon::now();
            \$undelete_{$lower_object_plural}->save();
            DB::table(config('access.permission_dependencies_table'))->insert([
                'permission_id' => \$undelete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('access.permission');
            \$permanently_delete_{$lower_object_plural} = new \$permission_model;
            \$permanently_delete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.permanently-delete';
            \$permanently_delete_{$lower_object_plural}->display_name = 'Permanently Delete {$this->object_plural}';
            \$permanently_delete_{$lower_object_plural}->system = true;
            \$permanently_delete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$permanently_delete_{$lower_object_plural}->sort = 14;
            \$permanently_delete_{$lower_object_plural}->created_at = Carbon::now();
            \$permanently_delete_{$lower_object_plural}->updated_at = Carbon::now();
            \$permanently_delete_{$lower_object_plural}->save();
            DB::table(config('access.permission_dependencies_table'))->insert([
                'permission_id' => \$permanently_delete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
EOD;

    }

    public function getEntity()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<EOD
<?php namespace Modules\\{$this->module}\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\\{$this->module}\Entities\Traits\Relationships\\{$this->object}Relationships;
use Modules\Base\Entities\BaseEntity;

class {$this->object} extends BaseEntity
{
    use {$this->object}Relationships;
    protected \$table = "{$lower_module}__{$lower_object}";
    protected \$fillable = [
        "name"
    ];
}
EOD;
    }

    public function getRelationships()
    {
        return <<<EOD
<?php  namespace Modules\\{$this->module}\Entities\Traits\Relationships;

trait {$this->object}Relationships
{

}
EOD;
    }

    public function getController()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
<?php namespace Modules\\{$this->module}\Http\Controllers;

use Modules\\{$this->module}\Http\Requests\\{$this->object_plural}\Create{$this->object}Request;
use Modules\\{$this->module}\Http\Requests\\{$this->object_plural}\Delete{$this->object}Request;
use Modules\\{$this->module}\Http\Requests\\{$this->object_plural}\Edit{$this->object}Request;
use Modules\\{$this->module}\Http\Requests\\{$this->object_plural}\Store{$this->object}Request;
use Modules\\{$this->module}\Repositories\\{$this->object_plural}Repository;
use Modules\Base\Http\Controllers\Controller;

class {$this->object_plural}Controller extends Controller {

	/**
	 * @var {$this->object_plural}Repository
	 */
	private \${$lower_object};

	public function __construct(
		{$this->object_plural}Repository \${$lower_object}
	) {
		\$this->{$lower_object} = \${$lower_object};
	}

	public function index()
	{
		\${$lower_object_plural} = \$this->{$lower_object}->paginate();
		return view('{$lower_module}::{$lower_object_plural}.index', compact('{$lower_object_plural}'));
	}

	/**
	 * @param  \$id
	 * @param  Create{$this->object}Request \$request
	 * @return mixed
	 */
	public function create(Create{$this->object}Request \$request)
	{
		return view('{$lower_module}::{$lower_object_plural}.create');
	}

	/**
	 * @param  Store{$this->object}Request \$request
	 * @return mixed
	 */
	public function store(Store{$this->object}Request \$request)
	{
		\$this->{$lower_object}->create(\$request->all());

        flash(__('{$lower_module}::{$lower_object}.actions.created'), 'success');
		return redirect()->route('admin.{$lower_module}.{$lower_object_plural}.index');
	}

	/**
	 * @param  \$id
	 * @param  Edit{$this->object}Request \$request
	 * @return mixed
	 */
	public function edit(\$id, Edit{$this->object}Request \$request)
	{
		\${$lower_object} = \$this->{$lower_object}->find(\$id);
		return view('{$lower_module}::{$lower_object_plural}.edit', compact('{$lower_object}'));
	}

	/**
	 * @param  \$id
	 * @param  Store{$this->object}Request \$request
	 * @return mixed
	 */
	public function update(\$id, Store{$this->object}Request \$request)
	{
		\$this->{$lower_object}->update(\$id, \$request->all());
        flash(__('{$lower_module}::{$lower_object}.actions.updated'), 'success');
		return redirect()->route('admin.{$lower_module}.{$lower_object_plural}.index', [\$id]);
	}

	/**
	 * @param  \$id
	 * @param  Delete{$this->object}Request \$request
	 * @return mixed
	 */
	public function destroy(\$id, Delete{$this->object}Request \$request)
	{
		\$this->{$lower_object}->destroy(\$id);
        flash(__('{$lower_module}::{$lower_object}.actions.deleted'), 'success');
		return redirect()->back();
	}
}
EOD;
    }

    public function getRequest($type)
    {
        $type = ucfirst($type);
        $lower_type = strtolower($type);
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<EOD
<?php namespace Modules\\{$this->module}\Http\Requests\\{$this->object_plural};

use App\Http\Requests\Request;

/**
 * Class {$type}{$this->object}Request
 * @package Modules\\{$this->module}\Http\Requests\\{$this->object_plural}
 */
class {$type}{$this->object}Request extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return access()->allow('{$lower_module}.{$lower_object}.{$lower_type}');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
EOD;
    }

    public function getRepository()
    {
        return <<<EOD
<?php namespace Modules\\{$this->module}\Repositories;

use Modules\Base\Repositories\BaseRepository;

interface {$this->object_plural}Repository extends BaseRepository
{

}
EOD;
    }

    public function getEloquentRepository()
    {
        return <<<EOD
<?php namespace Modules\\{$this->module}\Repositories\Eloquent;

use Modules\\{$this->module}\Repositories\\{$this->object_plural}Repository;
use Modules\Base\Repositories\Eloquent\EloquentBaseRepository;

class Eloquent{$this->object_plural}Repository extends EloquentBaseRepository implements {$this->object_plural}Repository
{

}
EOD;
    }
    
    public function getSidebarBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
@permission('{$lower_module}.view-{$lower_object_plural}-management')
    <li class="{{ Active::checkUriPattern('{$lower_module}/{$lower_object_plural}*') }}">
        <a href="{!! route('admin.{$lower_module}.{$lower_object_plural}.index') !!}"><span>{{ trans('{$lower_module}::{$lower_object}.uppercase.{$lower_object_plural}') }}</span></a>
    </li>
@endauth
EOD;
    }

    public function getCreateBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
@extends('{$lower_module}::layouts.master')

@section ('title', trans('{$lower_module}::titles.create-{$lower_object}'))

@section('page-header')
    <h1>{{ trans('buttons.general.crud.create') }} {{ trans('{$lower_module}::{$lower_object}.uppercase.{$lower_object}') }}</h1>
@endsection

@section('content')
    {!! Form::open(['route' => ['admin.{$lower_module}.{$lower_object_plural}.store'], 'class' => 'form-horizontal']) !!}

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('buttons.general.crud.create') }} <small>{{ trans('{$lower_module}::{$lower_object}.uppercase.new-{$lower_object}') }}</small></h3>
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="col-lg-12">
                @include('{$lower_module}::{$lower_object_plural}.form')
            </div>
        </div><!-- /.box-body -->
    </div><!--box-->

    <div class="box box-success">
        <div class="box-body">
            <div class="pull-left">
                <a href="{{ route('admin.{$lower_module}.{$lower_object_plural}.index') }}" class="btn btn-danger btn-xs">{{ trans('buttons.general.cancel') }}</a>
            </div>

            <div class="pull-right">
                <input type="submit" class="btn btn-success btn-xs" value="{{ trans('buttons.general.crud.create') }}" />
            </div>
            <div class="clearfix"></div>
        </div><!-- /.box-body -->
    </div><!--box-->

    {!! Form::close() !!}
@stop
EOD;
    }

    public function getEditBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
@extends('{$lower_module}::layouts.master')

@section ('title', trans('{$lower_module}::titles.edit-{$lower_object}'))

@section('page-header')
    <h1>{{ trans('buttons.general.crud.update') }} {{ trans('{$lower_module}::{$lower_object}.uppercase.{$lower_object}') }}</h1>
@endsection

@section('content')
    {!! Form::model(\${$lower_object}, ['route' => ['admin.{$lower_module}.{$lower_object_plural}.update', \${$lower_object}->id], 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'PATCH']) !!}

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('buttons.general.crud.edit') }} <small>{{ \${$lower_object}->name }}</small></h3>
        </div><!-- /.box-header -->

        <div class="box-body">
            <div class="col-lg-12">
                @include('{$lower_module}::{$lower_object_plural}.form')
            </div>
        </div><!-- /.box-body -->
    </div><!--box-->

    <div class="box box-success">
        <div class="box-body">
            <div class="pull-left">
                <a href="{{ route('admin.{$lower_module}.{$lower_object_plural}.index') }}" class="btn btn-danger btn-xs">{{ trans('buttons.general.cancel') }}</a>
            </div>

            <div class="pull-right">
                <input type="submit" class="btn btn-success btn-xs" value="{{ trans('buttons.general.crud.update') }}" />
            </div>
            <div class="clearfix"></div>
        </div><!-- /.box-body -->
    </div><!--box-->

    {!! Form::close() !!}
@stop
EOD;
    }

    public function getFormBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<EOD
<div class="form-group">
    {!! Form::label('name', trans('{$lower_module}::{$lower_object}.form.labels.name'), ['class' => 'control-label']) !!}
    <div class="control-input">
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('{$lower_module}::{$lower_object}.form.placeholders.name')]) !!}
    </div>
</div>
EOD;
    }

    public function getIndexBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<EOD
@extends('{$lower_module}::layouts.master')

@section ('title', trans('labels.backend.access.users.management'))

@section('page-header')
	<h1>{{ trans('{$lower_module}::{$lower_object}.uppercase.{$lower_object}-management') }}</h1>
@endsection

@section('content')
	<div>
		@permission('{$lower_module}.{$lower_object_plural}.add')
			{{ link_to_route('admin.{$lower_module}.{$lower_object_plural}.create', "+", null, []) }}
		@endauth
	</div>
	@include('base::items', [
		'items' => \${$lower_object_plural},
		'label' => trans('{$lower_module}::{$lower_object}.{$lower_object_plural}'),
	 	'cols' => [
			['header'=>'Name', 'attribute'=>'name']
		],
		'actions' => [
			['route'=> 'admin.{$lower_module}.{$lower_object_plural}.edit', 'title' => 'Edit', 'attributes' => [], 'permission' => '{$lower_module}.{$lower_object_plural}.edit'],
			['route'=> 'admin.{$lower_module}.{$lower_object_plural}.destroy', 'title' => 'Delete', 'attributes' => ['data-method'=>"delete", 'class' => 'btn btn-xs btn-danger' ], 'permission' => '{$this->module}.{$this->object_plural}.delete']
		]
	])
@stop
EOD;
    }

	public function getLanguageFile()
	{
		$lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);
		return <<<EOD
<?php
return [
    '{$lower_object_plural}' => '{$lower_object_plural}',
    '{$lower_object}' => '{$lower_object}',
    'uppercase' => [
        '{$lower_object_plural}' => '{$this->object_plural}',
        '{$lower_object}' => '{$this->object}',
        '{$lower_object}-management' => '{$this->object} Management',
        'new-{$lower_object}' => 'New {$this->object}'
    ],
    'form' => [
        'labels' => [
            'name' => 'Name'
        ],
        'placeholders' => [
            'name' => ''
        ]
    ],
    'actions' => [
        'created' => '{$this->object} created',
        'updated' => '{$this->object} updated',
        'deleted' => '{$this->object} deleted'
    ]
];
EOD;
	}
}
