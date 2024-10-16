<?php

namespace App\Classes;

use Illuminate\Support\Collection;

class HistoryQuery
{
    protected $orderBy = 'created_at';

    protected $sort = 'asc';

    
    protected $countryCode = null;

    
    protected $filters;

    public function __construct()
    {
        $this->filters = new Collection();
    }

    
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    
    public function getSort(): string
    {
        return $this->sort;
    }

    
    public function setOrderBy(string $orderBy)
    {
        $this->orderBy = $orderBy;
    }

    
    public function setSort(string $sort)
    {
        $this->sort = $sort;
    }

    
    public function getFilters(): Collection
    {
        return $this->filters;
    }

    public function addFilter(string $column, $value)
    {
        $this->filters->offsetSet($column, $value);
    }
}
