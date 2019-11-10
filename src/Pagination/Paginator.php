<?php

namespace Reddireccion\DryCrud\Pagination;

use \Illuminate\Pagination\Paginator as LaravelSimplePaginator;

class Paginator extends LaravelSimplePaginator
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'data' => $this->items->toArray(),
            'first_page_url' => $this->url(1),
            'from' => $this->firstItem(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path,
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
        ];
    }
    /**
     * Create a new Paginator instance for a given query.
     *
     * @param  QueryBuilder $query
     * @return Paginator
     */
    public static function createFromQuery($query)
    {
        $perPage = $query->getModel()->getPerPage();
        $page = static::resolveCurrentPage();
        $query->skip(($page - 1) * $perPage)->take($perPage + 1);
        return new Paginator($query->get(),$perPage,$page);
    }
}