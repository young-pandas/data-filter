<?php

namespace YoungPandas\DataFilter\Traits;

trait DateTimer
{
    public function validateDateTime($value): void
    {
        if (!is_null($value) && !$value instanceof \DateTime) {
            throw new \RuntimeException("The value must be an instance of DateTime. Given: " . gettype($value));
        }
    }

    public function filterDateTimeIn($value): \DateTime
    {
        // Sanitize the value for incoming data
        $sanitizedValue = $this->sanitizeDateTimeIn($value);

        // Validate the sanitized value
        $this->validateDateTime($sanitizedValue);

        return $sanitizedValue;
    }

    public function filterDateTimeOut($value): \DateTime
    {
        // Sanitize the value for outgoing data
        $sanitizedValue = $this->sanitizeDateTimeOut($value);

        // Validate the sanitized value
        $this->validateDateTime($sanitizedValue);

        return $sanitizedValue;
    }

    private function sanitizeDateTimeIn($value): \DateTime
    {
        // Add specific sanitization logic for incoming data
        return $value instanceof \DateTime ? $value : new \DateTime($value);
    }

    private function sanitizeDateTimeOut($value): \DateTime
    {
        // Add specific sanitization logic for outgoing data
        return $value instanceof \DateTime ? $value : new \DateTime($value);
    }
}
