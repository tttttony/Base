<?php namespace Modules\Base\Services;

interface FileServiceContract
{
    public function createBatch($files, $file_data = []);
    public function create($file, $file_data = []);
    public function createFromBase64String($base64_string, $file_data = []);
}