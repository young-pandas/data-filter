<?php

namespace YoungPandas\DataFilter\Traits;

use Illuminate\Support\Facades\Log;

trait Nullabler
{
    public function filterNullableIn(mixed $value = null): mixed
    {
        return is_null($value) ? null : $value;
    }

    public function filterNullableOut(mixed $value = null): mixed
    {
        return is_null($value) ? null : $value;
    }

    public function encryptNullableIn(mixed $value = null): mixed
    {
        return is_null($value) ? null : $value;
    }

    public function decryptNullableOut(mixed $value = null): mixed
    {
        return is_null($value) ? null : $value;
    }
}
