<?php namespace Modules\Base\Entities\Traits;

use Modules\Base\Exceptions\GeneralException;

trait Filterable
{
    protected $validFilterableFields = [];

    protected $filters = [];

    public function addFilter($key, $operator, $value = null)
    {
        if (func_num_args() == 2) {
            $value = $operator;
            $operator = '=';
        }

        if(!in_array($key, $this->validFilterableFields)) {
            return $this;
        }

        switch(strtolower($operator)) {
            case 'in':
            case'!in':
                if(!is_array($value))
                    throw new GeneralException(__('Unexpected Error: Filter value must be an array.'));
                break;
        }

        $filterMethod = 'filterBy' . camel_case($key);
        if( method_exists( $this, $filterMethod ) ) {
            $this->$filterMethod($value);
        } else {
            $this->filters[$key][] = ['value' => $value, 'operator' => $operator];
        }
        return $this;
    }

    protected function applyFiltersToQuery($query)
    {
        foreach($this->filters as $key => $comparisons) {
            foreach ($comparisons as $comparison) {
                if (str_contains($key, '.')) {
                    $relationships = explode('.', $key);
                    $key = array_pop($relationships);

                    if (method_exists($query->getModel(), 'shouldUseRelationship')
                        and $query->getModel()->shouldUseRelationship($relationships[0])) {
                        array_unshift($relationships, 'ssd');
                    }

                    $query->with(implode('.', $relationships));

                    if(in_array(strtolower($comparison['operator']), ['<>', '!in', '!null'])) {
                        //reverse negatives
                        $comparison['operator'] = str_replace(
                            ['<>', '!in', '!null'],
                            ['=', 'in', 'null'],
                            $comparison['operator']);
                        $comparison['reverse'] = true;
                    }

                    //TODO: Group relationships to lower the amount of exists in query
                    $query = $this->applyRelationships($query, $relationships, $key, $comparison);
                    continue;
                }

                if (method_exists($query->getModel(), 'shouldUse')
                    and !str_contains($key, '.')
                    and $query->getModel()->shouldUse($key, true)) {
                    //$key = 'ssd.'.$key;
                    $query = $this->applyRelationships($query, ['ssd'], $key, $comparison);
                    continue;
                }

                $this->alterQuery($query, $key, $comparison);
            }
        }
        return $query;
    }

    public function alterQuery($query, $key, $comparison) {
        $table = $query->getModel()->getTable();

        if (!is_array($comparison['value']) and !empty($comparison['value'])) {
            switch (strtolower($comparison['value'])) {
                case null:
                case 'null':
                    $query->whereNull($key);
                    break;
                case '!null':
                    $query->whereNotNull($key);
                    break;
            }
        }

        switch (strtolower($comparison['operator'])) {
            case 'in':
                $query->whereIn($table . '.' . $key, $comparison['value']);
                break;
            case'!in':
                $query->whereNotIn($table . '.' . $key, $comparison['value']);
                break;
            default:
                $query->where($table . '.' . $key, $comparison['operator'], $comparison['value']);
                break;
        }

        return $query;
    }

    /**
     * @param $query
     * @param $relationships
     * @return mixed
     */
    public function applyRelationships($query, $relationships, $key, $comparison) {
        $relationship = array_shift($relationships);

        if(empty($relationships)) {
            if(isset($comparison['reverse']) and $comparison['reverse']){
                return $query->whereDoesntHave($relationship, function ($q) use ($relationships, $key, $comparison) {
                    $this->alterQuery($q, $key, $comparison);
                });
            }
            //else
            return $query->whereHas($relationship, function ($q) use ($relationships, $key, $comparison) {
                $this->alterQuery($q, $key, $comparison);
            });
        }
        else {
            return $query->whereHas($relationship, function ($q) use ($relationships, $key, $comparison) {
                return $this->applyRelationships($q, $relationships, $key, $comparison);
            });
        }
    }
}
