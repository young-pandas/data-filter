<?php

namespace YoungPandas\DataFilter\Traits;

trait Callabler
{
    public function validateCallable($value): void
    {
        if (!is_null($value) && !is_callable($value)) {
            throw new \RuntimeException("The value must be callable. Given: " . gettype($value));
        }
    }

    public function filterCallableIn($value): callable
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeCallableIn($value);

        // Validate the sanitized value
        $this->validateCallable($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterCallableOut($value): callable
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeCallableOut($value);

        // Validate the sanitized value
        $this->validateCallable($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeCallableIn($value): callable
    {
        // Add specific sanitization logic for incoming data
        return is_callable($value) ? $value : function () {
        };
    }

    private function sanitizeCallableOut($value): callable
    {
        // Add specific sanitization logic for outgoing data
        return is_callable($value) ? $value : function () {
        };
    }
}
