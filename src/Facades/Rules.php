<?php

namespace YoungPandas\DataFilter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Rules
 * @package YoungPandas\DataFilter\Facades
 */
class Rules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'rules';
    }
}
