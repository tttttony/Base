<?php namespace Modules\Base\Providers;

use Illuminate\Support\ServiceProvider;

class BaseCommandsServiceProvider extends ServiceProvider
{

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * The available commands
	 *
	 * @var array
	 */
	protected $commands = [
		\Modules\Base\Console\TonyModule::class,
		\Modules\Base\Console\TonyObject::class,
	];

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->commands($this->commands);
	}
}