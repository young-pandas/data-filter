<?php

namespace YoungPandas\DataFilter\Traits;

trait Booler
{
    public function validateBool($value): void
    {
        if (!is_null($value) && !is_bool($value)) {
            throw new \RuntimeException("The value must be a boolean. Given: " . gettype($value));
        }
    }

    public function filterBoolIn($value): bool
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeBoolIn($value);

        // Validate the sanitized value
        $this->validateBool($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterBoolOut($value): bool
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeBoolOut($value);

        // Validate the sanitized value
        $this->validateBool($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeBoolIn($value): bool
    {
        // Add specific sanitization logic for incoming data
        return is_bool($value) ? $value : (bool)$value;
    }

    private function sanitizeBoolOut($value): bool
    {
        // Add specific sanitization logic for outgoing data
        return is_bool($value) ? $value : (bool)$value;
    }
}
