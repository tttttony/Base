<?php namespace Modules\Base\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Modules\Base\Entities\Traits\Filterable;
use Modules\Base\Services\Markdown;

class BaseEntity extends Model
{
    use Filterable;

    protected $activeOnly = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

    }

    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);

        if(!$this->activeOnly
            and method_exists($model, 'withInactive')) {
            $model->withInactive();
        }

        return $model;
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        if(
            method_exists($this, 'properties')
            and $this->properties() instanceof Relation
        ) {
            if(config('properties.site_code')) {
                $query->whereHas('properties', function ($q) {
                    $q->where('code', config('properties.site_code'));
                });
            }
        }

        if(in_array('active', $this->fillable) and $this->activeOnly) {
            $query->where('active', 1);
        }

        return $query;
    }

    protected function newRelatedInstance($class)
    {
        $class_instance = new $class;

        if(!$this->activeOnly
            and method_exists($class_instance, 'withInactive')) {
            $class_instance->withInactive();
        }

        return tap($class_instance, function ($instance) {
            if (! $instance->getConnectionName()) {
                $instance->setConnection($this->connection);
            }
        });
    }

    public function withInactive() {
        $this->activeOnly = false;
        return $this;
    }

    protected function castAttribute($key, $value)
    {
        if (is_null($value)) {
            return $value;
        }

        switch ($this->getCastType($key)) {
            case 'markdown':
                return new Markdown($value); // markdown
                break;
            default:
                break;
        }

        return parent::castAttribute($key, $value);
    }

    /**
     * Sync Relationships
     *
     * @param $relationship
     * @param $column
     * @param array $values
     * @param string $id_column
     */
    public function sync($relationship, array $values, $id_column = 'id')
    {
        $keep = $remove = $add = [];

        $new_values = array_filter($values);
        array_walk($new_values, function(&$item){ $item = intval($item); });

        /* HasOneOrMany */
        if(method_exists($this->$relationship(), 'getForeignKeyName')) {
            $old_values = $this->$relationship->pluck($id_column)->all();

            $keep = array_intersect($new_values, $old_values);
            $remove = array_diff($old_values, $new_values);
            $add = array_diff($new_values, $old_values);

            //remove
            $column = $this->$relationship()->getForeignKeyName();
            $this->$relationship()->whereIn($id_column, $remove)->each(function ($item) use ($column) {
                $item->$column = null;
                $item->save();
            });

            //add
            $this->$relationship()->saveMany($this->$relationship()->getModel()->findMany($add));
        }
        /* BelongsTo */
        else {
            if(count($values) > 0){
                $value = $values[0];

                if($value == $this->{$this->$relationship()->getForeignKey()}){
                    $keep = $value;
                }
                else {
                    $this->{$this->$relationship()->getForeignKey()} = $value;
                    $this->save();
                }
            }
        }

        return [
            'kept' => $keep,
            'removed' => $remove,
            'added' => $add
        ];
    }
}
