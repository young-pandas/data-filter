<?php

namespace YoungPandas\DataFilter\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

trait Cryptor
{
    private function validateEncrypted($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            $this->decryptValue($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function validateDecrypted($value): bool
    {
        if (!is_string($value)) {
            return true;
        }
        try {
            $this->decryptValue($value);
            return false;
        } catch (\Exception $e) {
            return true;
        }
    }

    public function filterEncryptIn($value): ?string
    {
        $sanitizedValue = $this->sanitizeEncryptIn($value);

        if (!$this->validateEncrypted($sanitizedValue)) {
            throw new \RuntimeException("The value must be encryptable. Given: " . gettype($sanitizedValue));
        }

        return $sanitizedValue;
    }

    public function filterDecryptOut($value): mixed
    {
        $sanitizedValue = $this->sanitizeDecryptOut($value);

        if (!$this->validateDecrypted($sanitizedValue)) {
            throw new \RuntimeException("The value must be decryptable. Given: " . gettype($sanitizedValue));
        }

        return $sanitizedValue;
    }

    private function sanitizeEncryptIn($value): ?string
    {
        return $this->encryptValue($value);
    }

    private function sanitizeDecryptOut($value): mixed
    {
        return $this->decryptValue($value);
    }

    private function encryptValue($value)
    {
        return Crypt::encrypt($value);
    }

    private function decryptValue($value)
    {
        return Crypt::decrypt($value);
    }
}
