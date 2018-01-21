<?php namespace Modules\Base\Repositories\Cache;

use Cache;
use Modules\Base\Repositories\BaseRepository;

abstract class CacheBaseDecorator implements BaseRepository
{
    /**
     * @var \Modules\Base\Repositories\BaseRepository
     */
    protected $repository;

    /**
     * @var string The entity name
     */
    protected $entityName;

    /**
     * @var int caching time
     */
    protected $cacheTime;

    public function __construct()
    {
        $this->cacheTime = config('cache.time', 1);
    }
    
    public function getCacheKey(){
        return $this->repository->getCacheKey();    
    }

    public function addFilter($key, $operator, $value = null) {
        $this->repository->addFilter($key, $operator, $value);
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 100)
    {
        return Cache::tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.paginate.{$perPage}", $this->cacheTime, function () use ($perPage) {
                return $this->repository->paginate($perPage);
            });
    }

    /**
     * @param string $name_column
     * @param string $id_column
     * @return mixed
     */
    public function getList($name_column = 'name', $id_column = 'id') {

        return Cache::tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.getList.{$name_column}.{$id_column}", $this->cacheTime, function () use ($name_column, $id_column) {
                return $this->repository->getList($name_column, $id_column);
            });
    }

    /**
     * @param  int   $id
     * @return mixed
     */
    public function find($id)
    {
        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.find.{$id}", $this->cacheTime,
                function () use ($id) {
                    return $this->repository->find($id);
                }
            );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.all", $this->cacheTime,
                function () {
                    return $this->repository->all();
                }
            );
    }

    /**
     * Find a resource by the given slug
     * @param  string $slug
     * @return object
     */
    public function findBySlug($slug)
    {
        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.findBySlug.{$slug}", $this->cacheTime,
                function () use ($slug) {
                    return $this->repository->findBySlug($slug);
                }
            );
    }

    /**
     * Create a resource
     *
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        Cache::tags($this->entityName)->flush();
        return $this->repository->create($data);
    }

    /**
     * Update a resource
     *
     * @param        $model
     * @param  array $data
     * @return mixed
     */
    public function update($model, $data)
    {
        Cache::tags($this->entityName)->flush();
        return $this->repository->update($model, $data);
    }

    /**
     * Destroy a resource
     *
     * @param $model
     * @return mixed
     */
    public function destroy($model)
    {
        Cache::tags($this->entityName)->flush();
        return $this->repository->destroy($model);
    }

    /**
     * Find a resource by an array of attributes
     * @param  array  $attributes
     * @return object
     */
    public function findByAttributes(array $attributes)
    {
        $tagIdentifier = json_encode($attributes);

        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.findByAttributes.{$tagIdentifier}", $this->cacheTime,
                function () use ($attributes) {
                    return $this->repository->findByAttributes($attributes);
                }
            );
    }

    /**
     * Get resources by an array of attributes
     * @param array $attributes
     * @param null|string $orderBy
     * @param string $sortOrder
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $tagIdentifier = json_encode($attributes);

        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.findByAttributes.{$tagIdentifier}.{$orderBy}.{$sortOrder}", $this->cacheTime,
                function () use ($attributes, $orderBy, $sortOrder) {
                    return $this->repository->getByAttributes($attributes, $orderBy, $sortOrder);
                }
            );
    }

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids)
    {
        $tagIdentifier = json_encode($ids);

        return Cache::
            tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.findByMany.{$tagIdentifier}", $this->cacheTime,
                function () use ($ids) {
                    return $this->repository->findByMany($ids);
                }
            );
    }

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache()
    {
        return Cache::tags($this->gentityName)->flush();
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return Cache::
        tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.first", $this->cacheTime,
                function ()  {
                    return $this->repository->first();
                }
            );
    }

    /**
     * @return mixed
     */
    public function listAll()
    {
        return Cache::
        tags($this->entityName, 'global')
            ->remember("{$this->getCacheKey()}.listAll", $this->cacheTime,
                function ()  {
                    return $this->repository->listAll();
                }
            );
    }
    /**
     * @param $item
     * @param $data
     * @param array $relationships
     * @return mixed
     */
    public function syncRelationships(&$item, $data, $relationships = [])
    {
        $this->repository->syncRelationships($item, $data, $relationships);
        return $this;
    }

    /**
     * @param $object
     * @param $item
     * @param array $object_ids
     * @return mixed
     */
    public function attachObject($object, &$item, $object_ids = [])
    {
        $this->repository->attachObject($object, $item, $object_ids);
        return $this;
    }

    public function query($reset = true)
    {
        $this->repository->query($reset);
        return $this;
    }

    public function withInactive()
    {
        $this->repository->withInactive();
        return $this;
    }

    public function getModelName()
    {
        $this->repository->getModelName();
        return $this;
    }

    public function getModelClass()
    {
        $this->repository->getModelClass();
        return $this;
    }

    public function sort($by, $order = 'asc')
    {
        $this->repository->sort($by, $order);
        return $this;
    }

    public function selected_relationships($id, $load_objects = false)
    {
        $this->repository->selected_relationships($id, $load_objects);
        return $this;
    }

    public function load($fields)
    {
        $this->repository->load($fields);
        return $this;
    }

    public function with($fields)
    {
        $this->repository->with($fields);
        return $this;
    }

    public function withCount($fields)
    {
        $this->repository->withCount($fields);
        return $this;
    }

    public function handleFiles(&$data)
    {
        $this->repository->handleFiles($data);
        return $this;
    }
}