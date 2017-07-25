<?php namespace Modules\Base\Entities\Transformers;

class BooleanTransformer extends BaseTransformer
{

    public function transform($boolean)
    {
        $data = [

            'result' => $boolean

        ];

        return $data;
    }

}