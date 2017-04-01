<?php namespace Modules\Base\Console;

use Illuminate\Support\Facades\Storage;

trait ScaffoldingBase
{
	protected function addLineToFile($filename, $search, $insert) {
		$replace = $search. "\n". $insert;
		Storage::disk('modules')->put($filename, str_replace($search, $replace, Storage::disk('modules')->get($filename)));
	}

	protected function put($file, $content)
	{
		$disk = Storage::disk('modules');

		if ($disk->has($file)) {
			$this->error($file . "      !! File already exists, not overwriting");
			return false;
		}

		Storage::disk('modules')->put($file, $content);
		$this->info($file . " created.");
	}
}