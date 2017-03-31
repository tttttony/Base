<?php namespace Modules\Base\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Base\Entities\Traits\Filterable;
use Modules\Base\Exceptions\GeneralException;
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

    //TODO: DESC and ASC should be constants
    protected $sortBy = 'id';
    protected $sortOrder = "DESC";

    /**
     * @param Model $model
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->validFilterableFields = [
            'slug', 'name', 'active'
        ];
    }

    public function query() {
        return $this->model->query();
    }

    protected function filterAndSort($query) {
        return $this->applySortToQuery($this->applyFiltersToQuery($query));
    }

    protected function applySortToQuery($query) {
        return $query->orderBy($this->sortBy, $this->sortOrder);
    }

    /**
     * @param $object
     * @param $item
     * @param array $object_ids
     * @return $this
     */
    public function attachObject($object, $item, $object_ids = []) {
        $method = 'attach'.ucfirst($object);

        if(method_exists($this, $method)) {
            $this->{$method}($item, $object_ids);
        }
        else {
            if(method_exists($item->{$object}(), 'sync'))
                $item->{$object}()->sync($object_ids, true);
            else
                $item->sync($object, $object_ids);
        }

        return $this;
    }

    /**
     * @param  int    $id
     * @return object
     */
    public function find($id)
    {
        if($item = $this->filterAndSort($this->query())->find($id)) {
            return $item;
        }
        throw new GeneralException(trans('Unexpected Error: Item not found.'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->filterAndSort($this->query())->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listAll($name_column = 'name', $id_column = 'id')
    {
        return $this->filterAndSort($this->query())->pluck($name_column, $id_column)->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function paginate($perPage = 100)
    {
        return $this->filterAndSort($this->query())->paginate($perPage);
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
        $this->addFilter('slug', $slug);
        return $this->filterAndSort($this->query())->first();
    }

    /**
     * Return a collection of elements who's ids match
     * @param array $ids
     * @return mixed
     */
    public function findByMany(array $ids)
    {
        $this->addFilter('id', 'in', $ids);
        return $this->filterAndSort($this->query())->get();
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