<?php namespace Modules\Base\Entities\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    protected function pre_transform(&$array) {
        //
    }

    protected function post_transform(&$array) {
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