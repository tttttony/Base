<?php
namespace Modules\Base\Console;

use DB;
use Illuminate\Console\Command;
use Storage;

class TonyModule extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tony:module {module}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Make a module scaffolding';

	protected $module = '';
	protected $module_plural = '';

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

		if ($this->checkModule()) {
			//Root Files
			$this->put($this->module . '/start.php', $this->getStartFile());
			$this->put($this->module . '/composer.json', $this->getComposerFile());
			$this->put($this->module . '/module.json', $this->getModuleFile());

			//Config
			$this->put($this->module . '/Config/config.php', $this->getConfigFile());

			//Databases
			$this->put($this->module . '/Database/Seeders/'.$this->module.'DatabaseSeeder.php', $this->getDatabaseSeederFile());
			$this->put($this->module . '/Database/Seeders/'.$this->module.'PermissionSeeder.php', $this->getPermissionSeederFile());

			//Resources
			$this->put($this->module . '/Resources/lang/en/lang.php', $this->getLanguageFile());
			$this->put($this->module . '/Resources/views/includes/sidebar.blade.php', $this->getSidebarFile());
			$this->put($this->module . '/Resources/views/layouts/master.blade.php', $this->getLayoutFile());

			//Misc.
			$this->put($this->module . '/Http/routes.php', $this->getRouteFile());
			$this->put($this->module . '/Providers/'.$this->module.'ServiceProvider.php', $this->getServiceProviderFile());

			$this->manualCodeChanges();
		}
	}

	public function manualCodeChanges()
	{
		$lower_module = strtolower($this->module);

		//Output lines of code that need to be added manually to other existing files.
		//Route
		//TODO: figure out what we all need to do after we create a module
/*
		$this->error("\n\n!!!! Don't forget to manually add the following lines: ");

		$this->line("\n\tHttp\\routes.php");
		$this->info(<<<CODE
	Route::resource('{$lower_object}s', '{$this->object}sController', ['as' => 'admin']);
CODE
		);
*/

		$this->info("Module ".$this->module." was created.");

		$this->line("\n");
	}

	public function checkModule()
	{
		$disk = Storage::disk('modules');

		if ($disk->has($this->module)) {
			$this->error($this->module . " already exists!");
			return false;
		}

		return true;
	}

	public function put($file, $content)
	{
		$disk = Storage::disk('modules');

		if ($disk->has($file)) {
			$this->error($file . "      !! File already exists, not overwriting");
			return false;
		}

		Storage::disk('modules')->put($file, $content);
		$this->info($file . " created.");
	}

	public function getStartFile()
	{
		return <<<EOD
<?php

/*
|--------------------------------------------------------------------------
| Register Namespaces And Routes
|--------------------------------------------------------------------------
|
| When a module starting, this file will executed automatically. This helps
| to register some namespaces like translator or view. Also this file
| will load the routes file for each module. You may also modify
| this file as you want.
|
*/

require __DIR__ . '/Http/routes.php';
EOD;

	}

	public function getComposerFile()
	{
		$lower_module = strtolower($this->module);

		return <<<EOD
{
	"name": "pingpong-modules/{$lower_module}",
	"description": "",
	"authors": [
		{
			"name": "Tony",
			"email": "tony@codewithtony.com"
		}
	],
	"autoload": {
		"psr-4": {
			"Modules\\{$this->module}\\": ""
		}
	}
}
EOD;

	}

	public function getModuleFile()
	{
		$lower_module = strtolower($this->module);

		return <<<EOD
{
    "name": "{$this->module}",
    "alias": "{$lower_module}",
    "description": "",
    "keywords": [],
    "active": 1,
    "order": 0,
    "providers": [
        "Modules\\\\{$this->module}\\\\Providers\\\\{$this->module}ServiceProvider"
    ],
    "aliases":{},
    "files": [
        "start.php"
    ]
}
EOD;

	}

	public function getConfigFile()
	{
		return <<<EOD
<?php

return [
	'name' => '{$this->module}'
];
EOD;

	}

	public function getDatabaseSeederFile()
	{
		return <<<EOD
<?php namespace Modules\\{$this->module}\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class {$this->module}DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		\$this->call({$this->module}PermissionSeeder::class);

		//Add Object files here
	}

}
EOD;

	}

	public function getPermissionSeederFile()
	{
		$lower_module = strtolower($this->module);
		return <<<EOD
<?php namespace Modules\\{$this->module}\Database\Seeders;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class {$this->module}PermissionSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		 /**
         * Create the Blog groups
         */
        \$group_model              = config('access.group');
        \$parent_group             = new \$group_model;
        \$parent_group->name       = '{$this->module}';
        \$parent_group->sort       = 1;
        \$parent_group->created_at = Carbon::now();
        \$parent_group->updated_at = Carbon::now();
        \$parent_group->save();

        /**
         * Permissions
         */
        \$permission_model              = config('access.permission');
        \$view_management               = new \$permission_model;
        \$view_management->name         = '{$lower_module}.view-management';
        \$view_management->display_name = 'View Management';
        \$view_management->system       = true;
        \$view_management->group_id     = \$parent_group->id;
        \$view_management->sort         = 2;
        \$view_management->created_at   = Carbon::now();
        \$view_management->updated_at   = Carbon::now();
        \$view_management->save();

		//add objects require statements below

	}
}
EOD;

	}

	public function getLanguageFile()
	{
		$lower_module = strtolower($this->module);
		return <<<EOD
<?php
return [
    '{$lower_module}s' => '{$lower_module}s',
    '{$lower_module}' => '{$lower_module}',
    'uppercase' => [
        '{$lower_module}s' => '{$this->module}s',
        '{$lower_module}' => '{$this->module}',
    ]
];
EOD;

	}

	public function getSidebarFile()
	{
		$lower_module = strtolower($this->module);
		return <<<EOD
@permission('{$lower_module}.view-management')
<li class="{{ Active::pattern('{$lower_module}/*') }} treeview">
    <a href="#">
    {{ trans('{$lower_module}::lang.uppercase.{$lower_module}') }}
    <i class="fa fa-angle-left pull-right"></i>
    </a>
    <ul class="treeview-menu {{ Active::pattern('{$lower_module}*', 'menu-open') }}" style="display: none; {{ Active::pattern('{$lower_module}*', 'display: block;') }}">

		{{-- object sidebar file go here --}}

    </ul>
</li>
@endauth
EOD;

	}

	public function getLayoutFile()
	{
		return <<<EOD
@extends('layouts.master')
EOD;

	}

	public function getRouteFile()
	{
		return <<<EOD
<?php
Route::group(['namespace' => 'Modules\\{$this->module}\Http\Controllers'], function(){
	Route::group(['domain' => 'admin'.Config::get('session.domain'), 'middleware' => ['web', 'admin', 'theme:admin']], function () {
		Route::group(['prefix' => '{$this->module}'], function()
		{

		});
	});

	Route::group(['domain' => 'retailers'.Config::get('session.domain'), 'namespace' => 'Retailer', 'middleware' => ['web', 'theme:retailer']], function () {
		Route::group(['prefix' => '{$this->module}'], function()
		{

		});
	});

	Route::group(['middleware' => ['web', 'theme:consumer']], function() {
		Route::group(['prefix' => '{$this->module}'], function()
		{

		});
	});
});
EOD;

	}

	public function getServiceProviderFile()
	{
		$lower_module = strtolower($this->module);
		return <<<EOD
<?php namespace Modules\\{$this->module}\Providers;

use Illuminate\Support\ServiceProvider;


class {$this->module}ServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected \$defer = false;

	/**
	 * Boot the application events.
	 * 
	 * @return void
	 */
	public function boot()
	{
		\$this->registerTranslations();
		\$this->registerConfig();
		\$this->registerViews();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\$this->registerBindings();
	}

	private function registerBindings()
	{
		//Object bindings go here
	}

	/**
	 * Register config.
	 * 
	 * @return void
	 */
	protected function registerConfig()
	{
		\$this->publishes([
		    __DIR__.'/../Config/config.php' => config_path('{$lower_module}.php'),
		]);
		\$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', '{$lower_module}'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		\$viewPath = base_path('resources/views/modules/{$lower_module}');

		\$sourcePath = __DIR__.'/../Resources/views';

		\$this->publishes([
			\$sourcePath => \$viewPath
		]);

		\$this->loadViewsFrom(array_merge(array_map(function (\$path) {
			return \$path . '/modules/{$lower_module}';
		}, \Config::get('view.paths')), [\$sourcePath]), '{$lower_module}');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		\$langPath = base_path('resources/lang/modules/{$lower_module}');

		if (is_dir(\$langPath)) {
			\$this->loadTranslationsFrom(\$langPath, '{$lower_module}');
		} else {
			\$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', '{$lower_module}');
		}
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}

EOD;

	}
}