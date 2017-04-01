<?php
namespace Modules\Base\Console;

use DB;
use Illuminate\Console\Command;
use Storage;

class TonyAttribute extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'tony:attribute {module} {object}';

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
	}
}