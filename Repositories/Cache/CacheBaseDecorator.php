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

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 100)
    {
        return Cache::tags($this->entityName, 'global')
            ->remember("{$this->entityName}.paginate.{$perPage}", $this->cacheTime, function () use ($perPage) {
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
            ->remember("{$this->entityName}.getList.{$name_column}.{$id_column}", $this->cacheTime, function () use ($name_column, $id_column) {
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
            ->remember("{$this->entityName}.find.{$id}", $this->cacheTime,
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
            ->remember("{$this->entityName}.all", $this->cacheTime,
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
            ->remember("{$this->entityName}.findBySlug.{$slug}", $this->cacheTime,
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
            ->remember("{$this->entityName}.findByAttributes.{$tagIdentifier}", $this->cacheTime,
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
            ->remember("{$this->entityName}.findByAttributes.{$tagIdentifier}.{$orderBy}.{$sortOrder}", $this->cacheTime,
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
            ->remember("{$this->entityName}.findByMany.{$tagIdentifier}", $this->cacheTime,
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
        return Cache::tags($this->entityName)->flush();
    }
}