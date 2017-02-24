<?php namespace Modules\Base\Repositories\Eloquent;

use Modules\Base\Exceptions\GeneralException;
use Illuminate\Database\Eloquent\Builder;
use Modules\Base\Repositories\BaseRepository;
/**
 * Class EloquentBaseRepository
 *
 * @package Modules\Base\Repositories\Eloquent
 */
abstract class EloquentBaseRepository implements BaseRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model An instance of the Eloquent Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @param  int    $id
     * @return object
     */
    public function find($id)
    {
        if($item = $this->model->find($id)) {
            return $item;
        }
        throw new GeneralException(trans('Unexpected Error: Item not found.'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->orderBy('created_at', 'DESC')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 100)
    {
        return $this->model->orderBy('created_at', 'DESC')->paginate($perPage);
    }

    /**
     * @param  mixed  $data
     * @return object
     */
    public function create($data)
    {
        return $this->model->create($data);
    }

    /**
     * @param $model
     * @param  array  $data
     * @return object
     */
    public function update($id, $data)
    {
        if($item = $this->find($id)) {
            $item->update($data);
            return $item;
        }
        throw new GeneralException(trans('Unexpected Error: Item not found.'));
    }

    /**
     * @param  Model $model
     * @return bool
     */
    public function destroy($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * Find a resource by the given slug
     *
     * @param  string $slug
     * @return object
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Find a resource by an array of attributes
     * @param  array  $attributes
     * @return object
     */
    public function findByAttributes(array $attributes)
    {
        $query = $this->buildQueryByAttributes($attributes);
        return $query->first();
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
        $query = $this->buildQueryByAttributes($attributes, $orderBy, $sortOrder);
        return $query->get();
    }
    /**
     * Build Query to catch resources by an array of attributes and params
     * @param array $attributes
     * @param null|string $orderBy
     * @param string $sortOrder
     * @return \Illuminate\Database\Query\Builder object
     */
    private function buildQueryByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->model->query();
        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }
        if (null !== $orderBy) {
            $query->orderBy($orderBy, $sortOrder);
        }
        return $query;
    }
    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids)
    {
        $query = $this->model->query();
        return $query->whereIn("id", $ids)->get();
    }
    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache()
    {
        return true;
    }
}