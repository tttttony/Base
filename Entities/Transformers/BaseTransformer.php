<?php namespace Modules\Base\Entities\Transformers;

use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    public function clearNullValues(&$array) {
        foreach($array as $k => $value)
            if (empty($value) && $value !== false) unset($array[$k]);
    }
}