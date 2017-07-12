<?php namespace Modules\Base\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Base\Entities\Traits\Filterable;

class BaseEntity extends Model
{
    use Filterable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function newQuery()
    {
        $query = parent::newQuery();

        if(
            method_exists($this, 'properties')
            and $this->properties() instanceof Relation
        ) {
            //$this->relationships[] = 'properties';
            //$this->validFilterableFields[] = 'properties.code';
            if(env('SITE_CODE')) {
                $query->whereHas('propertires', function ($q) {
                    $q->where('code', env('SITE_CODE'));
                });
            }
        }

        return $query;
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