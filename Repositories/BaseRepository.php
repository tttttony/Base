<?php namespace Modules\Base\Repositories;
/**
 * Interface BaseRepository
 * @package Modules\Base\Repositories
 */
interface BaseRepository
{
    /**
     * @param  int $id
     * @return $model
     */
    public function find($id);

    /**
     * Return a collection of all elements of the resource
     * @return mixed
     */
    public function all();

    public function listAll();

    public function paginate($perPage = 100);

    /**
     * Create a resource
     * @param $data
     * @return mixed
     */
    public function create($data);

    public function syncRelationships(&$item, $data, $relationships = []);
    public function attachObject($object, &$item, $object_ids = []);
    /**
     * Update a resource
     * @param $model
     * @param  array $data
     * @return mixed
     */
    public function update($model, $data);

    /**
     * Destroy a resource
     * @param $model
     * @return mixed
     */
    public function destroy($model);

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids);

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache();
}