<?php namespace Modules\Base\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Base\Entities\BaseEntity;
use Modules\Base\Entities\Traits\Filterable;
use Modules\Base\Exceptions\GeneralException;
use Modules\Base\Services\FileServiceContract;
use Modules\Base\Repositories\BaseRepository;
/**
 * Class EloquentBaseRepository
 *
 * @package Modules\Base\Repositories\Eloquent
 */
abstract class EloquentBaseRepository implements BaseRepository
{
    use Filterable;

    /**
     * @var \Illuminate\Database\Eloquent\Model An instance of the Eloquent Model
     */
    protected $model;
    protected $query;

    //TODO: DESC and ASC should be constants
    protected $sortBy = 'id';
    protected $sortOrder = "desc";

    protected $activeOnly = true;

    protected $with = [];
    protected $withCount = [];

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->sortBy = $this->model->getKeyName();
        if ($this->sortBy != 'id') {
            $this->sortOrder = "asc";
        }

        $this->sortable[] = 'id';
        $this->sortable[] = 'slug';
        $this->sortable[] = 'name';

        $this->validFilterableFields[] = 'id';
        $this->validFilterableFields[] = 'slug';
        $this->validFilterableFields[] = 'name';
        $this->validFilterableFields[] = 'active';

        if(empty($this->relationships)) {
            $this->relationships = [];
        }

        if(
            method_exists($this->model, 'properties')
            and $this->model->properties() instanceof Relation
        ) {
            $this->relationships[] = 'properties';
            $this->validFilterableFields[] = 'properties.code';
            if(config('properties.site_code')) {
                $this->addFilter('properties.code', config('properties.site_code'));
            }
        }
    }

    public function query($reset = true)
    {
        if (isset($this->query) and !$reset) {
            return $this->query;
        }
        $this->query = $this->model;
        return $this->query;
    }

    public function withInactive() {
        $this->activeOnly = false;
        $this->model->withInactive();
        return $this;
    }

    public function getModelName() {
        return class_basename($this->model);
    }

    public function getModelClass() {
        return get_class($this->model);
    }

    public function sort($by, $order = 'asc')
    {
        if (in_array($by, $this->sortable)) {
            $this->sortBy = ($by) ?: $this->model->getKeyName();
            $this->sortOrder = ($order) ?: "asc";
        }
        return $this;
    }

    protected function filterAndSort($query)
    {
        return $this->applySortToQuery(
            $this->applyFiltersToQuery(
                $this->applyWithCount(
                    $this->applyWith(
                        $query
                    )
                )
            )
        );
    }

    protected function applySortToQuery($query)
    {
        return $query->orderBy($this->sortBy, $this->sortOrder);
    }

    protected function applyWith($query)
    {
        return $query->with($this->with);
    }

    protected function applyWithCount($query)
    {
        if(!empty($this->withCount) and is_array($this->withCount)) {
            if(isset($this->withCount['top'])) {
                $query = $query->withCount($this->withCount['top']);
            }
            foreach ($this->withCount as $key => $count) {
                if($key !== 'top') {
                    $query = $query->with([
                        $key => function ($q) use ($count) {
                            $q->withCount($count);
                        }
                    ]);
                }
            }
        }
        return $query;
    }

    public function syncRelationships(&$item, $data, $relationships = [], $new = false)
    {
        if ($item instanceof BaseEntity) {
            if (empty($relationships)) {
                if (!property_exists($this, 'relationships') || !is_array($this->relationships)) {
                    return $this;
                }
                $relationships = $this->relationships;
            }

            foreach ($relationships as $relationship) {
                if (config('properties.site_code') != null and $relationship == 'properties') {
                    if($new and config('properties.site_code')) {
                        $this->attachObject('properties', $item, [config('properties.site_code')]);
                    }
                    continue;
                }

                if (isset($data[$relationship]) || (isset($data['_method']) and $data['_method'] == 'PUT')) {
                    $relationship_data = [];
                    if(isset($data[$relationship])) {
                        $relationship_data =  (is_array($data[$relationship])) ? $data[$relationship] : [$data[$relationship]];
                    }
                    $this->attachObject($relationship, $item, $relationship_data);
                }
            }

            return $this;
        }
        throw new GeneralException(__('not found'));
    }

    /**
     * @param $object
     * @param $item
     * @param array $object_ids
     * @return $this
     */
    public function attachObject($object, &$item, $object_ids = [])
    {
        $method = 'attach' . ucfirst($object);

        if (method_exists($this, $method)) {
            $this->{$method}($item, $object_ids);
        } else {
            if (method_exists($item->{$object}(), 'sync')) {
                $new_ids = $object_ids;

                if (!empty($object_ids) and property_exists($this, 'attachWithAttributeInPivot') and in_array($object, $this->attachWithAttributeInPivot)) {
                    foreach ($object_ids as $key => $id) {
                        $new_ids[$id] = ['attribute_name' => $object];
                    }
                }

                $item->{$object}()->sync($new_ids, true);
            } else {
                $item->sync($object, $object_ids);
            }
        }

        return $this;
    }

    /**
     * @param  int $id
     * @return object
     */
    public function find($id)
    {
        //first check the id
        if (
            ( is_numeric($id) or ! $this->model->getIncrementing() or is_array($id))
            and $item = $this->filterAndSort($this->query())->find($id) /* intentional assignment */
        ) {
            return $item;
        }

        //then if check slug
        if (
            ! is_int($id)
            and method_exists($this->model, 'lookUpBySlug')
            and $items_collection = $this->model->lookUpBySlug($id) /* intentional assignment */
            and count($items_collection) > 0
        ) {
            $slug = $items_collection->first();
            return $this->filterAndSort($this->query())->find($slug->sluggable_id);
        }


        throw new GeneralException(trans('Unexpected Error: Item not found.'));
    }

    public function selected_relationships($id, $load_objects = false)
    {
        $return = [];
        $item = $this->model->find($id);

        $load_objects_for = [];
        if($load_objects === true) {
            $load_objects_for = $this->relationships;
        }
        elseif (is_array($load_objects)) {
            $load_objects_for = $load_objects;
        }

        foreach ($this->relationships as $relationship) {
            if (method_exists($this, 'selected_' . $relationship)) {
                $return[$relationship] = $this->{'selected_' . $relationship}($item, $load_objects_for);
            } elseif (is_callable([$item, $relationship])) {
                if(in_array($relationship, $load_objects_for)) {
                    $return[$relationship] = $item->$relationship->all();
                }
                else {
                    $return[$relationship] = $item->$relationship->pluck($this->model->{$relationship}()->getRelated()->getKeyName())->all();
                }
            }
        }
        return $return;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->filterAndSort($this->query())->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first()
    {
        return $this->filterAndSort($this->query())->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listAll($name_column = 'name')
    {
        return $this->filterAndSort($this->query())->pluck($name_column, $this->model->getKeyName())->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 100)
    {
        return $this->filterAndSort($this->query())->paginate($perPage);
    }

    public function load($fields)
    {
        $this->model->load($fields);
        return $this;
    }

    public function with($fields)
    {
        $this->with = $fields;
        return $this;
    }

    public function withCount($fields)
    {
        $this->withCount = $fields;
        return $this;
    }

    /**
     * @param  mixed  $data
     * @return object
     */
    public function create($data)
    {
        // $data['active'] = isset($data['active']) ? 1 : 0;
        $item = $this->model->create($data);

        if(!property_exists($this, 'filesService')
            or !in_array(FileServiceContract::class, class_implements($this->filesService))) {
            $this->syncRelationships($item, (isset($data)) ? $data : [], [], true);
        }
        else {
            $this->handleFiles($data)->syncRelationships($item, (isset($data)) ? $data : [], [], true);
        }

        /*

            Huge hack for sluggables, should be fixed somehow, race condition
            Maybe touching the parent's timestamps on property relationship update?

        */
        //$item = $this->find($item->id);
        $item->save();

        return $item;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($id, $data)
    {
        // $data['active'] = isset($data['active']) ? 1 : 0;
        if($item = $this->model->find($id)) {
            if(!property_exists($this, 'filesService')
                or !in_array(FileServiceContract::class, class_implements($this->filesService))) {
                $this->syncRelationships($item, (isset($data)) ? $data : []);
            }
            else {
                $this->handleFiles($data)->syncRelationships($item, (isset($data)) ? $data : []);
            }
            $item->update($data);
            return $item;
        }
        throw new GeneralException(trans('Unexpected Error: Item not found.'));
    }

    /**
     * @param  int $id
     * @return bool
     */
    public function destroy($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids)
    {
        return $this->filterAndSort($this->query())->findMany($ids);
    }

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache()
    {
        return true;
    }

    public function handleFiles(&$data) {
        if(!property_exists($this, 'filesService')
            or !in_array(FileServiceContract::class, class_implements($this->filesService))) {
            throw new GeneralException('file service must be set');
        }

        $keys = array_merge(
            (!empty($data['files-new']))? array_keys($data['files-new']): [],
            (!empty($data['files-keep']))? array_keys($data['files-keep']): []
        );

        if (!empty($keys)) {
            foreach ($keys as $key) {
                $data[$key] = [];
                if((!empty($data['files-keep']) and !empty($data['files-keep'][$key]))) {
                    if(isset($data['files-keep'][$key]['id'])) {
                        $data[$key] = $data['files-keep'][$key]['id'];
                    }
                    else {
                        $data[$key] = array_column($data['files-keep'][$key], 'id');
                    }
                }

                $needs_to_remove = true;
                if (!empty($data['files'][$key])) {
                    if(is_array($data['files'][$key])) {
                        $ids = $this->filesService->createBatch($data['files'][$key], $data['files-new'][$key]);
                        foreach ($ids as $new_id) {
                            $data[$key][] = $new_id;
                        }
                    }
                    else {
                        $needs_to_remove = false;
                        $image_object = $this->filesService->create($data['files'][$key], $data['files-new'][$key]);
                        $data[$key] = $image_object->id;
                    }
                }

                if (!empty($data['files-remove'][$key]) && !empty($data[$key]) and $needs_to_remove) {
                    foreach ($data[$key] as $k => $file) {
                        if (in_array($file, $data['files-remove'][$key])) {
                            unset($data[$key][$k]);
                        }
                    }
                }
            }
        }

        return $this;
    }
}
