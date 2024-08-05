<?php

namespace YoungPandas\DataFilter\Traits;

trait Floater
{
    public function validateFloat($value): void
    {
        if (!is_null($value) && !is_float($value)) {
            throw new \RuntimeException("The value must be a float. Given: " . gettype($value));
        }
    }

    public function filterFloatIn($value): float
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeFloatIn($value);

        // Validate the sanitized value
        $this->validateFloat($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterFloatOut($value): float
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeFloatOut($value);

        // Validate the sanitized value
        $this->validateFloat($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeFloatIn($value): float
    {
        // Add specific sanitization logic for incoming data
        return is_float($value) ? $value : (float)$value;
    }

    private function sanitizeFloatOut($value): float
    {
        // Add specific sanitization logic for outgoing data
        return is_float($value) ? $value : (float)$value;
    }
}
