<?php

namespace YoungPandas\DataFilter\Traits;

trait Resourcer
{
    public function validateResource($value): void
    {
        if (!is_null($value) && !is_resource($value)) {
            throw new \RuntimeException("The value must be a resource. Given: " . gettype($value));
        }
    }

    public function filterResourceIn($value)
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeResourceIn($value);

        // Validate the sanitized value
        $this->validateResource($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterResourceOut($value)
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeResourceOut($value);

        // Validate the sanitized value
        $this->validateResource($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeResourceIn($value)
    {
        // Add specific sanitization logic for incoming data
        return is_resource($value) ? $value : null;
    }

    private function sanitizeResourceOut($value)
    {
        // Add specific sanitization logic for outgoing data
        return is_resource($value) ? $value : null;
    }
}
