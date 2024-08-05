<?php

namespace YoungPandas\DataFilter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Service
 * @package YoungPandas\DataFilter\Facades
 */
class Service extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'service';
    }
}
