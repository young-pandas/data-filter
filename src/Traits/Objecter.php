<?php

namespace YoungPandas\DataFilter\Traits;

trait Objecter
{
    public function validateObject($value): void
    {
        if (!is_null($value) && !is_object($value)) {
            throw new \RuntimeException("The value must be an object. Given: " . gettype($value));
        }
    }

    public function filterObjectIn($value): object
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeObjectIn($value);

        // Validate the sanitized value
        $this->validateObject($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterObjectOut($value): object
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeObjectOut($value);

        // Validate the sanitized value
        $this->validateObject($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeObjectIn($value): object
    {
        // Add specific sanitization logic for incoming data
        return is_object($value) ? $value : (object)$value;
    }

    private function sanitizeObjectOut($value): object
    {
        // Add specific sanitization logic for outgoing data
        return is_object($value) ? $value : (object)$value;
    }
}
