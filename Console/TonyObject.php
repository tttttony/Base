<?php
namespace Modules\Base\Console;

use DB;
use Illuminate\Console\Command;
use Storage;

class TonyObject extends Command
{
	use ScaffoldingBase;
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
	    $lower_object_plural = strtolower($this->object_plural);

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
        $this->put($this->module . '/Resources/views/' . $lower_object_plural . '/create.blade.php', $this->getCreateBlade());
        $this->put($this->module . '/Resources/views/' . $lower_object_plural . '/edit.blade.php', $this->getEditBlade());
        $this->put($this->module . '/Resources/views/' . $lower_object_plural . '/form.blade.php', $this->getFormBlade());
	    $this->put($this->module . '/Resources/views/' . $lower_object_plural . '/index.blade.php', $this->getIndexBlade());
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
		$lower_module_plural = strtolower($this->module_plural);
		$lower_object_plural = strtolower($this->object_plural);

		$this->addLineToFile(
			$this->module . '/Routes/web.php',
			'/** ROUTES **/', <<<CODE
Route::resource('{$lower_object_plural}', '\Modules\\{$this->module}\Http\Controllers\\{$this->object_plural}Controller', ['as' => 'admin.{$lower_module}']);
CODE
		);

		$this->addLineToFile(
			$this->module . '/Database/Seeders/'.$this->module.'PermissionSeeder.php',
			'/** OBJECT PERMISSIONS **/', <<<CODE
	\$this->call({$this->object}PermissionSeeder::class);
CODE
		);

		$this->addLineToFile(
			$this->module . '/Providers/'.$this->module.'ServiceProvider.php',
			'/** BINDINGS **/', <<<CODE
		\$this->app->bind(
			\Modules\\{$this->module}\Repositories\\{$this->object_plural}Repository::class,
			function () {
				\$repository = new \Modules\\{$this->module}\Repositories\Eloquent\Eloquent{$this->object_plural}Repository(new \Modules\\{$this->module}\Entities\\{$this->object}());
				return \$repository;
			}
		);

CODE
		);

		$this->addLineToFile(
			$this->module . '/Resources/views/includes/sidebar.blade.php',
			'/** LINKS **/', <<<CODE
	@include('{$lower_module}::includes.{$lower_object_plural}-sidebar')
CODE
		);
	}

    public function getCreateTableFile()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<FILE
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
FILE;

    }

    public function getPermissionSeeder()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
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
        \$permission_model = config('base.permission');
        \$view_management = \$permission_model::where('name', '{$lower_module}.view-management')->first();
        \$parent_group_id = \$view_management->group_id;

	    \$group_model = config('base.group');
	    \${$lower_object}_group = new \$group_model;
	    \${$lower_object}_group->name = '{$this->module}';
	    \${$lower_object}_group->sort = 1;
	    \${$lower_object}_group->parent_id = \$parent_group_id;
	    \${$lower_object}_group->created_at = Carbon::now();
	    \${$lower_object}_group->updated_at = Carbon::now();
	    \${$lower_object}_group->save();

        \$permission_model = config('base.permission');
        \$view_{$lower_object_plural}_management = new \$permission_model;
        \$view_{$lower_object_plural}_management->name = '{$lower_module}.{$lower_object}.view-management';
        \$view_{$lower_object_plural}_management->display_name = 'View {$this->object_plural} Management';
        \$view_{$lower_object_plural}_management->system = true;
        \$view_{$lower_object_plural}_management->group_id = \$parent_group_id;
        \$view_{$lower_object_plural}_management->sort = 5;
        \$view_{$lower_object_plural}_management->created_at = Carbon::now();
        \$view_{$lower_object_plural}_management->updated_at = Carbon::now();
        \$view_{$lower_object_plural}_management->save();
        DB::table(config('base.permission_dependencies_table'))->insert([
            'permission_id' => \$view_{$lower_object_plural}_management->id,
            'dependency_id' => \$view_management->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        {
            \$permission_model = config('base.permission');
            \$create_{$lower_object_plural} = new \$permission_model;
            \$create_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.create';
            \$create_{$lower_object_plural}->display_name = 'Create {$this->object_plural}';
            \$create_{$lower_object_plural}->system = true;
            \$create_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$create_{$lower_object_plural}->sort = 5;
            \$create_{$lower_object_plural}->created_at = Carbon::now();
            \$create_{$lower_object_plural}->updated_at = Carbon::now();
            \$create_{$lower_object_plural}->save();
            DB::table(config('base.permission_dependencies_table'))->insert([
                'permission_id' => \$create_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('base.permission');
            \$edit_{$lower_object_plural} = new \$permission_model;
            \$edit_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.edit';
            \$edit_{$lower_object_plural}->display_name = 'Edit {$this->object_plural}';
            \$edit_{$lower_object_plural}->system = true;
            \$edit_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$edit_{$lower_object_plural}->sort = 5;
            \$edit_{$lower_object_plural}->created_at = Carbon::now();
            \$edit_{$lower_object_plural}->updated_at = Carbon::now();
            \$edit_{$lower_object_plural}->save();
            DB::table(config('base.permission_dependencies_table'))->insert([
                'permission_id' => \$edit_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('base.permission');
            \$delete_{$lower_object_plural} = new \$permission_model;
            \$delete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.delete';
            \$delete_{$lower_object_plural}->display_name = 'Delete {$this->object_plural}';
            \$delete_{$lower_object_plural}->system = true;
            \$delete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$delete_{$lower_object_plural}->sort = 5;
            \$delete_{$lower_object_plural}->created_at = Carbon::now();
            \$delete_{$lower_object_plural}->updated_at = Carbon::now();
            \$delete_{$lower_object_plural}->save();
            DB::table(config('base.permission_dependencies_table'))->insert([
                'permission_id' => \$delete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('base.permission');
            \$undelete_{$lower_object_plural} = new \$permission_model;
            \$undelete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.undelete';
            \$undelete_{$lower_object_plural}->display_name = 'Restore {$this->object_plural}';
            \$undelete_{$lower_object_plural}->system = true;
            \$undelete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$undelete_{$lower_object_plural}->sort = 13;
            \$undelete_{$lower_object_plural}->created_at = Carbon::now();
            \$undelete_{$lower_object_plural}->updated_at = Carbon::now();
            \$undelete_{$lower_object_plural}->save();
            DB::table(config('base.permission_dependencies_table'))->insert([
                'permission_id' => \$undelete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            \$permission_model = config('base.permission');
            \$permanently_delete_{$lower_object_plural} = new \$permission_model;
            \$permanently_delete_{$lower_object_plural}->name = '{$lower_module}.{$lower_object}.permanently-delete';
            \$permanently_delete_{$lower_object_plural}->display_name = 'Permanently Delete {$this->object_plural}';
            \$permanently_delete_{$lower_object_plural}->system = true;
            \$permanently_delete_{$lower_object_plural}->group_id = \${$lower_object}_group->id;
            \$permanently_delete_{$lower_object_plural}->sort = 14;
            \$permanently_delete_{$lower_object_plural}->created_at = Carbon::now();
            \$permanently_delete_{$lower_object_plural}->updated_at = Carbon::now();
            \$permanently_delete_{$lower_object_plural}->save();
            DB::table(config('base.permission_dependencies_table'))->insert([
                'permission_id' => \$permanently_delete_{$lower_object_plural}->id,
                'dependency_id' => \$view_{$lower_object_plural}_management->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
FILE;

    }

    public function getEntity()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<FILE
<?php namespace Modules\\{$this->module}\Entities;

use Laracasts\Presenter\PresentableTrait;
use Modules\\{$this->module}\Entities\Traits\Relationships\\{$this->object}Relationships;
use Modules\Base\Entities\BaseEntity;

class {$this->object} extends BaseEntity
{
    use {$this->object}Relationships, PresentableTrait;
    protected \$presenter = 'Modules\Base\Entities\Presenters\BasePresenter';
    protected \$table = "{$lower_module}__{$lower_object}";
    protected \$fillable = [
        "name"
    ];
}
FILE;
    }

    public function getRelationships()
    {
        return <<<FILE
<?php  namespace Modules\\{$this->module}\Entities\Traits\Relationships;

trait {$this->object}Relationships
{

}
FILE;
    }

    public function getController()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
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
	private \${$lower_object_plural}Repository;

	public function __construct(
		{$this->object_plural}Repository \${$lower_object_plural}Repository
	) {
		\$this->{$lower_object_plural}Repository = \${$lower_object_plural}Repository;
	}

	public function index()
	{
		\${$lower_object_plural} = \$this->{$lower_object_plural}Repository->paginate();
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
		\$this->{$lower_object_plural}Repository->create(\$request->all());

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
		\${$lower_object} = \$this->{$lower_object_plural}Repository->find(\$id);
		return view('{$lower_module}::{$lower_object_plural}.edit', compact('{$lower_object}'));
	}

	/**
	 * @param  \$id
	 * @param  Store{$this->object}Request \$request
	 * @return mixed
	 */
	public function update(\$id, Store{$this->object}Request \$request)
	{
		\$this->{$lower_object_plural}Repository->update(\$id, \$request->all());
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
		\$this->{$lower_object_plural}Repository->destroy(\$id);
        flash(__('{$lower_module}::{$lower_object}.actions.deleted'), 'success');
		return redirect()->back();
	}
}
FILE;
    }

    public function getRequest($type)
    {
        $type = ucfirst($type);
        $lower_type = strtolower($type);
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<FILE
<?php namespace Modules\\{$this->module}\Http\Requests\\{$this->object_plural};

use Modules\Base\Http\Requests\Request;

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
FILE;
    }

    public function getRepository()
    {
        return <<<FILE
<?php namespace Modules\\{$this->module}\Repositories;

use Modules\Base\Repositories\BaseRepository;

interface {$this->object_plural}Repository extends BaseRepository
{

}
FILE;
    }

    public function getEloquentRepository()
    {
        return <<<FILE
<?php namespace Modules\\{$this->module}\Repositories\Eloquent;

use Modules\\{$this->module}\Repositories\\{$this->object_plural}Repository;
use Modules\Base\Repositories\Eloquent\EloquentBaseRepository;

class Eloquent{$this->object_plural}Repository extends EloquentBaseRepository implements {$this->object_plural}Repository
{

}
FILE;
    }
    
    public function getSidebarBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
return [
    'permission' => '{$lower_module}.view-{$lower_object_plural}-management',
    'route' => 'admin.{$lower_module}.{$lower_object_plural}.index',
    'title' => __('{$lower_module}::{$lower_object}.uppercase.{$lower_object_plural}'),
];
FILE;
    }

    public function getCreateBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
@extends('pages.default')

@section ('title', __('{$lower_module}::titles.create-{$lower_object}'))

@section('page-header')
    {{ __('{$lower_module}::titles.create-{$lower_object}') }}
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ URL::route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ URL::route('admin.{$lower_module}.{$lower_object_plural}.index') }}">{{ __('{$lower_module}::{$lower_object}.uppercase.{$lower_object_plural}') }}</a></li>
        <li class="breadcrumb-item active">{{ __('buttons.general.crud.create') }}</li>
    </ol>
@endsection

@section('page-actions')
    <a href="{{ route('admin.{$lower_module}.{$lower_object_plural}.index') }}" class="btn btn-white" role="button">{{ __('buttons.general.cancel') }}</a>
    <button class="btn btn-primary" type="submit" form="{$lower_object_plural}_form">{{ __('buttons.general.crud.create') }}</button>
@endsection

@section('content')
    {!! Form::open(['route' => ['admin.{$lower_module}.{$lower_object_plural}.store'], 'id' => '{$lower_object_plural}_form']) !!}
    @include('{$lower_module}::{$lower_object_plural}.form')
    {!! Form::close() !!}
@stop
FILE;
    }

    public function getEditBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
@extends('pages.default')

@section ('title', trans('{$lower_module}::titles.edit-{$lower_object}'))

@section('page-header')
    {{ __('buttons.general.crud.update') }} {{ __('{$lower_module}::{$lower_object}.uppercase.{$lower_object}') }}
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ URL::route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ URL::route('admin.{$lower_module}.{$lower_object_plural}.index') }}">{{ __('{$lower_module}::{$lower_object}.uppercase.{$lower_object_plural}') }}</a></li>
        <li class="breadcrumb-item active">{{ \${$lower_object}->name }}</li>
    </ol>
@endsection

@section('page-actions')
    <a href="{{ route('admin.{$lower_module}.{$lower_object_plural}.index') }}" class="btn btn-white" role="button">{{ __('buttons.general.cancel') }}</a>
    <button class="btn btn-primary" type="submit" form="{$lower_object_plural}_form">{{ __('buttons.general.crud.update') }}</button>
@endsection

@section('content')
    {!! Form::model(\${$lower_object}, ['route' => ['admin.{$lower_module}.{$lower_object_plural}.update', \${$lower_object}->id], 'id' => '{$lower_object_plural}_form', 'role' => 'form', 'method' => 'PATCH']) !!}
        @include('{$lower_module}::{$lower_object_plural}.form')
    {!! Form::close() !!}
@stop
FILE;
    }

    public function getFormBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);

        return <<<FILE
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                
            </div>
            <div class="card-block">
                @include('inputs.text', ['input' => ['name' => 'name', 'label' => '{$lower_module}::{$lower_object}.form.labels.name']])
            </div>
        </div>
    </div>
</div>
FILE;
    }

    public function getIndexBlade()
    {
        $lower_module = strtolower($this->module);
        $lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);

        return <<<FILE
@extends('pages.default')

@section ('title', __('{$lower_module}::{$lower_object}.uppercase.{$lower_object}-management'))

@section('page-header')
	{{ __('{$lower_module}::{$lower_object}.uppercase.{$lower_object}-management') }}
@endsection

@section('page-actions')
	@permission('{$lower_module}.{$lower_object_plural}.add')
	<a href="{{ URL::route('admin.{$lower_module}.{$lower_object_plural}.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i>
    </a>
	@endauth
@endsection

@section('content')
	<div class="card panel-default">
		<div class="card-block">
			@include('base::items', [
				'items' => \${$lower_object_plural},
				'label' => __('{$lower_module}::{$lower_object}.{$lower_object_plural}'),
			    'cols' => [
					['header'=>'Name', 'attribute'=>'name']
				],
				'actions' => [
					['route'=> 'admin.{$lower_module}.{$lower_object_plural}.edit', 'title' => 'Edit', 'icon' => 'pencil', 'attributes' => [ 'class' => 'btn btn-sm btn-primary' ], 'permission' => '{$lower_module}.{$lower_object_plural}.edit'],
					['route'=> 'admin.{$lower_module}.{$lower_object_plural}.destroy', 'title' => 'Delete', 'icon' => 'trash-o', 'template' => 'actions.delete', 'attributes' => ['data-method'=>"delete", 'class' => 'btn btn-sm btn-danger delete-button' ], 'permission' => '{$lower_module}.{$lower_object_plural}.delete']
				]
			])
		</div>
	</div>
@stop
FILE;
    }

	public function getLanguageFile()
	{
		$lower_object = strtolower($this->object);
        $lower_object_plural = strtolower($this->object_plural);
		return <<<FILE
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
FILE;
	}
}
