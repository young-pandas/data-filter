<?php

namespace YoungPandas\DataFilter\Traits;

trait Stringer
{
    public function validateString($value): void
    {
        if (!is_null($value) && !is_string($value)) {
            throw new \RuntimeException("The value must be a string. Given: " . gettype($value));
        }
    }

    public function filterStringIn($value): string
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeStringIn($value);

        // Validate the sanitized value
        $this->validateString($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterStringOut($value): string
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeStringOut($value);

        // Validate the sanitized value
        $this->validateString($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeStringIn($value): string
    {
        // Add specific sanitization logic for incoming data
        return is_string($value) ? $value : (string)$value;
    }

    private function sanitizeStringOut($value): string
    {
        // Add specific sanitization logic for outgoing data
        return is_string($value) ? $value : (string)$value;
    }
}
