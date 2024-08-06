<?php

namespace YoungPandas\DataFilter\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use YoungPandas\DataFilter\Contracts\RulesContract;
use YoungPandas\DataFilter\Contracts\DataFilterContract;

/**
 * This class is responsible for applying rules to the data.
 * 
 * @package YoungPandas\DataFilter\Services
 */
class RulesService implements RulesContract
{
    private DataFilterContract $dataFilter;
    private static array $rulesCache = [];

    private function __construct(DataFilterContract $dataFilter)
    {
        $this->dataFilter = $dataFilter;
    }

    // Factory method to create an instance of RulesService
    public static function create(DataFilterContract $dataFilter): self
    {
        return new self($dataFilter);
    }

    /**
     * @param array $data
     * @param string $rulesFilePath
     * @param string $prefix
     * @return array
     */
    public function applyRules(array $data, string $rulesFilePath, string $prefix): array
    {
        try {
            $loadedData = $this->loadJsonRules($rulesFilePath);
            return $this->validateKeys($data, $loadedData['rules'], $prefix, $loadedData['suffix']);
        } catch (\Exception $e) {
            throw new \RuntimeException("RulesService::applyRules: " . $e->getMessage(), 0, $e);
        }
    }

    private function loadJsonRules(string $filePath): array
    {
        $enableCaching = Config::boolean('data-filter.enableCaching', false);

        if ($enableCaching && isset(self::$rulesCache[$filePath])) {
            return self::$rulesCache[$filePath];
        }

        if (!file_exists($filePath)) {
            throw new \RuntimeException("RulesService::loadJsonRules: Rules file not found: $filePath");
        }

        $data = $this->decodeJsonFile($filePath);
        $suffix = $this->determineSuffix($filePath);

        if ($enableCaching) {
            self::$rulesCache[$filePath] = ['rules' => $data, 'suffix' => $suffix];
        }

        return ['rules' => $data, 'suffix' => $suffix];
    }

    private function decodeJsonFile(string $filePath): array
    {
        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("RulesService::decodeJsonFile: Error decoding JSON from file: $filePath");
        }
        return $data;
    }

    private function determineSuffix(string $filePath): string
    {
        if (strpos($filePath, 'Requests') !== false) {
            return 'In';
        } elseif (strpos($filePath, 'Responses') !== false) {
            return 'Out';
        }
        return '';
    }

    private function validateKeys(array $data, array $rules, string $prefix, string $suffix, int $depth = 0): array
    {
        $this->checkMissingKeys($data, $rules);

        $endToEndEncryption = Config::boolean('data-filter.endToEndEncryption');

        $validatedData = [];
        foreach ($rules as $key => $rule) {
            $validatedData[$key] = $this->processKey($data, $key, $rule, $prefix, $suffix, $depth, $endToEndEncryption);
        }

        return $validatedData;
    }

    private function checkMissingKeys(array $data, array &$rules): void
    {
        $dataKeys = array_keys($data);
        $ruleKeys = array_keys($rules);
        $missingKeys = array_diff($ruleKeys, $dataKeys);

        foreach ($missingKeys as $missingKey) {
            if ($this->isNullable($rules[$missingKey])) {
                unset($rules[$missingKey]);
                continue;
            }
            throw new \RuntimeException("RulesService::validateKeys: Missing key in data: $missingKey");
        }
    }

    private function isNullable($rule): bool
    {
        return is_array($rule) && in_array('nullable', $rule) || $rule === 'nullable';
    }

    private function processKey(array $data, string $key, $rule, string $prefix, string $suffix, int $depth, bool $endToEndEncryption)
    {
        if (is_array($data[$key] ?? null) && is_array($rule)) {
            return $this->validateKeys($data[$key] ?? [], $rule, $prefix, $suffix, $depth + 1);
        }

        return $this->applyRulesForKey($data[$key] ?? null, $rule, $prefix, $suffix, $endToEndEncryption, $depth);
    }

    private function applyRulesForKey($value, $rule, string $prefix, string $suffix, bool $endToEndEncryption, int $depth)
    {
        $rulesForKey = is_array($rule) ? $rule : [$rule];

        if ($endToEndEncryption) {
            $value = $this->applyEndToEndEncryptionRules($value, $rulesForKey, $prefix, $suffix);
        } else {
            $value = $this->applyStandardRules($value, $rulesForKey, $prefix, $suffix);
        }

        return $value;
    }

    private function applyEndToEndEncryptionRules($value, array $rulesForKey, string $prefix, string $suffix)
    {
        if ($suffix === 'Out') {
            $value = $this->applyRule($value, 'decrypt', $prefix, $suffix);
        }

        foreach ($rulesForKey as $singleRule) {
            if ($singleRule === 'nullable' && !isset($value)) {
                return null;
            }
            if ($singleRule !== 'encrypt' && $singleRule !== 'decrypt') {
                $value = $this->applyRule($value, $singleRule, $prefix, $suffix);
            }
        }

        if ($suffix === 'In') {
            $value = $this->applyRule($value, 'encrypt', $prefix, $suffix);
        }

        return $value;
    }

    private function applyStandardRules($value, array $rulesForKey, string $prefix, string $suffix)
    {
        if ($suffix === 'Out' && (in_array('decrypt', $rulesForKey) || in_array('encrypt', $rulesForKey))) {
            $value = $this->applyRule($value, 'decrypt', $prefix, $suffix);
            $rulesForKey = array_diff($rulesForKey, ['decrypt', 'encrypt']);
        }

        foreach ($rulesForKey as $singleRule) {
            if ($singleRule === 'nullable' && !isset($value)) {
                return null;
            }
            if ($singleRule !== 'encrypt' && $singleRule !== 'decrypt') {
                $value = $this->applyRule($value, $singleRule, $prefix, $suffix);
            }
        }

        if ($suffix === 'In' && (in_array('decrypt', $rulesForKey) || in_array('encrypt', $rulesForKey))) {
            $value = $this->applyRule($value, 'encrypt', $prefix, $suffix);
            $rulesForKey = array_diff($rulesForKey, ['decrypt', 'encrypt']);
        }

        return $value;
    }

    private function applyRule(mixed $value, mixed $rule, string $prefix, string $suffix): mixed
    {
        if ($rule === 'nullable' && $value === null) {
            return $value;
        }
        if ($rule === 'nullable') {
            $prefix = 'filter';
        }
        $methodName = strtolower($prefix) . ucfirst($rule) . ucfirst($suffix);

        if (!method_exists($this->dataFilter, $methodName)) {
            throw new \RuntimeException("RulesService::applyRule: Method $methodName does not exist in DataFilter");
        }

        return $this->dataFilter->$methodName($value);
    }
}
