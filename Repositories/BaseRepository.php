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

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return mixed
     */
    public function listAll();

    /**
     * @param int $perPage
     * @return mixed
     */
    public function paginate($perPage = 100);

    /**
     * Create a resource
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @param $item
     * @param $data
     * @param array $relationships
     * @return mixed
     */
    public function syncRelationships(&$item, $data, $relationships = []);

    /**
     * @param $object
     * @param $item
     * @param array $object_ids
     * @return mixed
     */
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
    public function getCacheKey();
    public function query($reset = true);
    public function withInactive();
    public function getModelName();
    public function getModelClass();
    public function sort($by, $order = 'asc');
    public function selected_relationships($id, $load_objects = false);
    public function load($fields);
    public function with($fields);
    public function withCount($fields);
    public function handleFiles(&$data);
}