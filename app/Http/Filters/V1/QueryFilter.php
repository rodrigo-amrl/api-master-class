<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected $builder;
    protected $sortable = [];
    public function __construct(protected Request $request) {}
    public function filter($array)
    {
        foreach ($array as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $this->builder;
    }
    public function apply(Builder $builder)
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $name => $value) {
            if (method_exists($this, $name)) {
                $this->$name($value);
            }
        }

        return $this->builder;
    }
    protected function sort($value)
    {
        $sorteAttributes = explode(',', $value);

        foreach ($sorteAttributes as $attribute) {
            $direction = 'asc';
            if (strpos($attribute, '-') === 0) {
                $direction = 'desc';
                $attribute = substr($attribute, 1);
            }
            if (!in_array($attribute, $this->sortable) && !array_key_exists($attribute, $this->sortable))
                continue;

            $columnName = $this->sortable[$attribute] ?? $attribute;
            $this->builder->orderBy($columnName, $direction);
        }
    }
}
