<?php

namespace Modules\Base\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Modules\Base\Repositories\BaseRepository;

class Controller extends BaseController
{
    protected $repository;
    public function __construct($repository = null) {
        if($repository instanceof BaseRepository) {
            $this->repository = $repository;
        }
    }

    /**
     * Handle Site Specific Data
     *
     * @param $data ($ssd)
     */
    public function handleSsd($id, $request) {
        if($request->has('sites_data')) {
            $ssd = $request->input('sites_data');
            foreach ($ssd as $site_code => $data) {
                if(
                    $site_code == env('SITE_CODE')
                    or (!empty($request->input('properties')) and in_array($site_code, $request->input('properties')))
                ) {
                    $repo_class = 'Sites\\' . strtoupper($site_code) . '\Repositories\Eloquent\\' . class_basename($this->repository);

                    if (!class_exists($repo_class)) {
                        $repo_class = get_class($this->repository);
                    }

                    $model_class = 'Sites\\' . strtoupper($site_code) . '\Entities\\' . $this->repository->getModelName();

                    if (class_exists($model_class)) {
                        $object = new $repo_class(new $model_class);
                        $object->update($id, $data);
                    }
                }
            }
        }
    }

    // TODO: next three methods need to be DRY'ed
    public function updateWithImages($request) {
        $images = $request->has('files-keep-data.*.id')? $request->input('files-keep-data.*.id'): [];
        $remove_images = $request->has('files-remove-data.*.id')? $request->input('files-remove-data.*.id'): [];

        if($request->hasFile("images")) {
            // fileService is declared by the extending controller
            $ids = $this->fileService->createBatch($request->file("images"), ($request->only('files-data')) ? $request->only('files-data') : []);
            foreach($ids as $new_id) {
                $images[] = $new_id;
            }
        }
        if(!empty($remove_images) && !empty($images)) {
            foreach($images as $k => $image) {
                if(in_array($image, $remove_images)) {
                    unset($images[$k]);
                }
            }
        }
        return $images;
    }

    public function updateWithFile($request) {
        $files = $request->has('files-keep-data.*.id')? $request->input('files-keep-data.*.id'): [];
        $remove_files = $request->has('files-remove-data.*.id')? $request->input('files-remove-data.*.id'): [];

        if($request->hasFile("files")) {
            // fileService is declared by the extending controller
            $ids = $this->fileService->createBatch($request->file("files"), ($request->only('files-data')) ? $request->only('files-data') : []);
            foreach($ids as $new_id) {
                $files[] = $new_id;
            }
        }
        if(!empty($remove_files) && !empty($files)) {
            foreach($files as $k => $file) {
                if(in_array($file, $remove_files)) {
                    unset($files[$k]);
                }
            }
        }
        return $files;
    }

    public function updateWithImage($request) {
        $image = $request->input('files-keep-data.*.id');
        // TODO: Abstract handling images/files to the base controller
        if($request->hasFile("image")) {
            $new_image = $this->fileService->create($request->file("image"), ($request->only('files-data')) ? $request->only('files-data') : []);
            $image = [$new_image->id];
        }
        return $image;
    }
}
