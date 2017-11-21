<?php namespace Modules\Base\Entities\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    protected $merge_data;

    protected function pre_transform(&$array, $item) {
        $this->merge_data = [];
        $counts = [];
        if(empty($identifier)) {
            if(app('Illuminate\Http\Request')->has('count'))
                $counts = explode(',', app('Illuminate\Http\Request')->input('count'));
        }

        foreach($counts as $count) {
            $this->merge_data[$count.'_count'] = $item->{$count.'_count'};
        }
    }

    protected function post_transform(&$array, $item) {
        $this->clearNullValues($array);
        $identifier = $this->getCurrentScope()->getIdentifier();

        $fields = [];
        if(!empty($identifier)) {
            $fields = $this->getCurrentScope()->getManager()->getIncludeParams($identifier)->get('fields');
        }
        else {
            if(app('Illuminate\Http\Request')->has('fields'))
                $fields = explode(',', app('Illuminate\Http\Request')->input('fields'));
        }
        if(!empty($fields))
            $array = $this->reduceKeys($array, $fields);

        $array = array_merge($array, $this->merge_data);
    }

    protected function clearNullValues(&$array) {
        foreach($array as $k => $value)
            if (empty($value) && $value !== false) unset($array[$k]);
    }

    protected function reduceKeys($array, $fields) {
        if(!in_array('id', $fields))
            array_unshift($fields, 'id');

        $return = [];
        foreach($fields as $key) $return[$key] = $array[$key];
        return $return;
    }
}