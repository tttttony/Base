<?php namespace Modules\Base\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the application events.
	 * 
	 * @return void
	 */
	public function boot(Router $router)
	{
		$this->registerTranslations();
		$this->registerConfig();
		$this->registerViews();
		$this->registerBladeExtentions();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerBindings();
	}

	private function registerBindings()
	{
        $this->app->bind(
            \Modules\Base\Services\UserServiceContract::class,
            \Modules\Base\Services\UserService::class
        );
        $this->app->bind(
            \Modules\Base\Repositories\BaseRepository::class,
            function(){ return new \Modules\Base\Repositories\Eloquent\EloquentBaseRepository(new \Modules\Base\Entities\BaseEntity()); }
        );
	}

	/**
	 * Register config.
	 * 
	 * @return void
	 */
	protected function registerConfig()
	{
		$this->publishes([
		    __DIR__.'/../Config/config.php' => config_path('base.php'),
		]);
		$this->mergeConfigFrom(
		    __DIR__.'/../Config/config.php', 'base'
		);
	}

	/**
	 * Register views.
	 * 
	 * @return void
	 */
	public function registerViews()
	{
		$viewPath = base_path('resources/views/modules/base');

		$sourcePath = __DIR__.'/../Resources/views';

		$this->publishes([
			$sourcePath => $viewPath
		]);

		$this->loadViewsFrom(array_merge(array_map(function ($path) {
			return $path . '/modules/base';
		}, \Config::get('view.paths')), [$sourcePath]), 'base');
	}

	/**
	 * Register translations.
	 * 
	 * @return void
	 */
	public function registerTranslations()
	{
		$langPath = base_path('resources/lang/modules/base');

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, 'base');
		} else {
			$this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'base');
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

	public function registerBladeExtentions() {
        Blade::directive('wrap', function($expression){
            return "<?php \$wrapper = $expression; ob_start(); ?>";
        });

        Blade::directive('endwrap', function(){
            return "<?php \$output = ob_get_contents(); ob_end_clean(); echo preg_replace(\"/<(label|input|select|textarea)(.*?)(name|for)=\\\"(.*?)?(\\[])?\\\"/\", \"<$1$2$3=\\\"\".\$wrapper.\"[$4]$5\\\"\", \$output); ?>";
        });
    }

}
