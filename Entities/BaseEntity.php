<?php namespace Modules\Base\Entities;

use Illuminate\Database\Eloquent\Model;

class BaseEntity extends Model
{

    /**
     * Sync hasOne and belongTo Relationships
     *
     * @param $relationship
     * @param $column
     * @param array $values
     * @param string $id_column
     */
    public function sync($relationship, $column, array $values, $id_column = 'id')
    {
        $new_values = array_filter($values);
        array_walk($new_values, function(&$item, $key){
            $item = intval($item);
        });

        $old_values = $this->$relationship->pluck($id_column)->all();

        $keep = array_intersect($new_values, $old_values);
        $remove = array_diff($old_values, $new_values);
        $add = array_diff($new_values, $old_values);

        //remove
        $this->$relationship()->whereIn($id_column, $remove)->each(function($item, $key) use ($column) {
            $item->$column = null;
            $item->save();
        });

        //add
        $new = [];
        foreach($add as $id){
            $new[] = $this->$relationship()->getModel()->find($id);
        }
        $this->$relationship()->saveMany($new);

        return [
            'kept' => $keep,
            'removed' => $remove,
            'added' => $add
        ];
    }

}