<?php

namespace YoungPandas\DataFilter\Traits;

trait Inter
{
    public function validateInt($value): void
    {
        if (!is_null($value) && !is_int($value)) {
            throw new \RuntimeException("The value must be an integer. Given: " . gettype($value));
        }
    }

    public function filterIntIn($value): int
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeIntIn($value);

        // Validate the sanitized value
        $this->validateInt($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterIntOut($value): int
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeIntOut($value);

        // Validate the sanitized value
        $this->validateInt($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeIntIn($value): int
    {
        // Add specific sanitization logic for incoming data
        return is_int($value) ? $value : (int)$value;
    }

    private function sanitizeIntOut($value): int
    {
        // Add specific sanitization logic for outgoing data
        return is_int($value) ? $value : (int)$value;
    }
}
