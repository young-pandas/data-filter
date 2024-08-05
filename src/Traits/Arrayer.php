<?php

namespace YoungPandas\DataFilter\Traits;

trait Arrayer
{
    public function validateArray($value): void
    {
        if (!is_null($value) && !is_array($value)) {
            throw new \RuntimeException("The value must be an array. Given: " . gettype($value));
        }
    }

    public function filterArrayIn($value): array
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeArrayIn($value);

        // Validate the sanitized value
        $this->validateArray($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterArrayOut($value): array
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeArrayOut($value);

        // Validate the sanitized value
        $this->validateArray($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeArrayIn($value): array
    {
        // Add specific sanitization logic for incoming data
        return is_array($value) ? $value : (array)$value;
    }

    private function sanitizeArrayOut($value): array
    {
        // Add specific sanitization logic for outgoing data
        return is_array($value) ? $value : (array)$value;
    }
}
