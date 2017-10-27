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
                    list($relationship, $key) = explode('.', $key);

                    if (property_exists($this, 'relationships') and in_array($relationship, $this->relationships)) {
                        $query->with($relationship)->whereHas($relationship, function ($q) use ($key, $comparison) {
                            $table = $q->getModel()->getTable();

                            if (!empty($comparison['value'])) {
                                switch (strtolower($comparison['value'])) {
                                    case null:
                                    case 'null':
                                        $q->whereNull($key);
                                        break;
                                    case '!null':
                                        $q->whereNotNull($key);
                                        break;
                                }
                            }

                            switch (strtolower($comparison['operator'])) {
                                case 'in':
                                    $q->whereIn($table . '.' . $key, $comparison['value']);
                                    break;
                                case'!in':
                                    $q->whereNotIn($table . '.' . $key, $comparison['value']);
                                    break;
                                default:
                                    $q->where($table . '.' . $key, $comparison['operator'], $comparison['value']);
                                    break;
                            }
                        });
                    }
                    continue;
                }

                if (!empty($comparison['value'])) {
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
                        $query->whereIn($key, $comparison['value']);
                        break;
                    case'!in':
                        $query->whereNotIn($key, $comparison['value']);
                        break;
                    default:
                        $query->where($key, $comparison['operator'], $comparison['value']);
                        break;
                }
            }
        }
        return $query;
    }
}