<?php

namespace YoungPandas\DataFilter\Helpers;

use YoungPandas\DataFilter\Contracts\DataFilterContract;
use YoungPandas\DataFilter\Traits\Arrayer;
use YoungPandas\DataFilter\Traits\Booler;
use YoungPandas\DataFilter\Traits\Callabler;
use YoungPandas\DataFilter\Traits\Cryptor;
use YoungPandas\DataFilter\Traits\DateTimer;
use YoungPandas\DataFilter\Traits\Floater;
use YoungPandas\DataFilter\Traits\Inter;
use YoungPandas\DataFilter\Traits\Nullabler;
use YoungPandas\DataFilter\Traits\Objecter;
use YoungPandas\DataFilter\Traits\Resourcer;
use YoungPandas\DataFilter\Traits\Stringer;

class DataFilter implements DataFilterContract
{
    use Arrayer,
        Booler,
        Callabler,
        Cryptor,
        DateTimer,
        Floater,
        Inter,
        Nullabler,
        Objecter,
        Resourcer,
        Stringer;
}
