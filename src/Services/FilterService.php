<?php

namespace YoungPandas\DataFilter\Services;

use Illuminate\Support\Facades\Config;
use YoungPandas\DataFilter\Services\RulesService;
use YoungPandas\DataFilter\Contracts\RulesContract;
use YoungPandas\DataFilter\Contracts\ServiceContract;
use YoungPandas\DataFilter\Contracts\DataFilterContract;

/**
 * Class FilterService
 * This class is a base class for all services.
 * It provides methods to filter request and response data.
 * It is an abstract class and cannot be instantiated.
 * 
 * @package YoungPandas\DataFilter\Services
 * 
 * @property DataFilterContract $dataFilter
 * @property RulesContract $rulesService
 * 
 * @method __construct(DataFilterContract $dataFilter)
 * @method filterDataArray(array $data, string $rulesFilePath): array
 * @method filterDataObject(array $data, string $rulesFilePath): object
 * @method filterRequestData(array $data, string $rulesFilePath): array
 * @method filterResponseData(array $data, string $rulesFilePath): object
 */
abstract class FilterService implements ServiceContract
{
    protected RulesContract $rulesService;
    protected DataFilterContract $dataFilter;

    public function __construct(DataFilterContract $dataFilter)
    {
        $this->dataFilter = $dataFilter;
        $this->rulesService = RulesService::create($this->dataFilter);
    }

    /**
     * This method filters the data using the rules file provided.
     * It validates and sanitizes the data based on the rules.
     * 
     * @param array $data Pass the data to be filtered.
     * @param string $rulesFilePath Pass the rules file path to be used for filtering.
     * @return array Return the filtered data as an array.
     */
    public function filterDataArray(array $data, string $rulesFilePath, string $methodPrefix = 'filter'): array
    {
        try {
            if (empty($data) || empty($rulesFilePath)) {
                throw new \RuntimeException("Data or rules file path is empty");
            }

            $rulesFolderPath = Config::string('data-filter.rulesFolderPath');
            // Ensure there is a proper directory separator between the folder path and file path
            $fullPath = rtrim($rulesFolderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($rulesFilePath, DIRECTORY_SEPARATOR);

            if (empty($sanitizedData = $this->rulesService->applyRules($data, $fullPath, $methodPrefix))) {
                throw new \RuntimeException("Data sanitization failed");
            }
            return $sanitizedData;
        } catch (\Exception $e) {
            throw new \RuntimeException("FilterService::filterDataArray: " . $e->getMessage());
        }
    }

    /**
     * This method filters the data using the rules file provided.
     * It validates and sanitizes the data based on the rules.
     * 
     * @param array $data Pass the data to be filtered.
     * @param string $rulesFilePath Pass the rules file path to be used for filtering.
     * @return object Return the filtered data as an object.
     */
    public function filterDataObject(array $data, string $rulesFilePath, string $methodPrefix = 'filter'): object
    {
        try {
            if (empty($data) || empty($rulesFilePath)) {
                throw new \RuntimeException("Data or rules file path is empty");
            }

            $rulesFolderPath = Config::string('data-filter.rulesFolderPath');
            // Ensure there is a proper directory separator between the folder path and file path
            $fullPath = rtrim($rulesFolderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($rulesFilePath, DIRECTORY_SEPARATOR);

            if (empty($sanitizedData = $this->rulesService->applyRules($data, $fullPath, $methodPrefix))) {
                throw new \RuntimeException("Data sanitization failed");
            }
            return (object) $sanitizedData;
        } catch (\Exception $e) {
            throw new \RuntimeException("FilterService::filterDataObject: " . $e->getMessage());
        }
    }

    /**
     * This method filters the request data using the rules file provided.
     * It validates and sanitizes the request data based on the rules.
     * 
     * @param array $data Pass the request data to be filtered.
     * @param string $rulesFilePath Pass the rules file path to be used for filtering.
     * @param string $methodPrefix Pass the method prefix to be used for filtering.
     * @return array Return the filtered request data as an array.
     */
    public function filterRequestData(array $data, string $rulesFilePath, string $methodPrefix = 'filter'): array
    {
        try {
            if (empty($data) || empty($rulesFilePath)) {
                throw new \RuntimeException("Data or rules file path is empty");
            }
            // Load the requests folder path from the configuration file
            $requestsFolderPath = Config::string('data-filter.requestsFolderPath');
            if (empty($requestsFolderPath)) {
                throw new \RuntimeException("Requests folder path is not configured");
            }
            // Ensure there is a proper directory separator between the folder path and file path
            $fullPath = rtrim($requestsFolderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($rulesFilePath, DIRECTORY_SEPARATOR);
            if (empty($sanitizedData = $this->rulesService->applyRules($data, $fullPath, $methodPrefix))) {
                throw new \RuntimeException("Data sanitization failed");
            }
            return $sanitizedData;
        } catch (\Exception $e) {
            throw new \RuntimeException("FilterService::filterRequestData: " . $e->getMessage());
        }
    }

    /**
     * This method filters the response data using the rules file provided.
     * It validates and sanitizes the response data based on the rules.
     * 
     * @param array $data Pass the response data to be filtered.
     * @param string $rulesFilePath Pass the rules file path to be used for filtering.
     * @param string $methodPrefix Pass the method prefix to be used for filtering.
     * @return object Return the filtered response data as an object.
     */
    public function filterResponseData(array $data, string $rulesFilePath, string $methodPrefix = 'filter'): object
    {
        try {
            if (empty($data) || empty($rulesFilePath)) {
                throw new \RuntimeException("Data or rules file path is empty");
            }
            // Load the responses folder path from the configuration file
            $responsesFolderPath = Config::string('data-filter.responsesFolderPath');
            // Ensure there is a proper directory separator between the folder path and file path
            $fullPath = rtrim($responsesFolderPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($rulesFilePath, DIRECTORY_SEPARATOR);

            if (empty($sanitizedData = $this->rulesService->applyRules($data, $fullPath, $methodPrefix))) {
                throw new \RuntimeException("Data sanitization failed");
            }
            return (object) $sanitizedData;
        } catch (\Exception $e) {
            throw new \RuntimeException("FilterService::filterResponseData: " . $e->getMessage());
        }
    }
}
