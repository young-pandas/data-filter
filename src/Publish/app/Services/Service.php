<?php

namespace App\Services;

use App\Helpers\DataFilter;
use YoungPandas\DataFilter\Contracts\DataFilterContract;
use YoungPandas\DataFilter\Services\FilterService;

/**
 * This class creates a new instance of the FilterService and provides additional functionality
 * by utilizing the DataFilter helper class using the DataFilterContract interface.
 *
 * @property DataFilterContract $dataFilter
 * 
 */
class Service extends FilterService
{
    protected DataFilterContract $dataFilter;

    public function __construct()
    {
        $this->dataFilter = new DataFilter();

        parent::__construct($this->dataFilter);
    }
}
