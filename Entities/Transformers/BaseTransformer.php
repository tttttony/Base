<?php namespace Modules\Base\Entities\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    protected $merge_data = [];

    protected function pre_transform(&$array, $item) {
        $this->merge_data = [];
        $counts = [];
        $identifier = $this->getCurrentScope()->getIdentifier();

        if(!empty($identifier)) {
            $counts = $this->getCurrentScope()->getManager()->getIncludeParams($identifier)->get('count');
        }
        else {
            if(app('Illuminate\Http\Request')->has('count'))
                $counts = explode(',', app('Illuminate\Http\Request')->input('count'));
        }

        if(!empty($counts)) {
            foreach ($counts as $count) {
                $this->merge_data[$count . '_count'] = $item->{$count . '_count'};
            }
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

        $merge_in = $this->merge_data;
        $array = array_merge($array, $merge_in);
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