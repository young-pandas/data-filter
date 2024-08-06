<?php

namespace YoungPandas\DataFilter\Contracts;

/**
 * Service Contract
 * @package YoungPandas\DataFilter\Contracts
 */
interface ServiceContract
{
    public function filterDataArray(array $data, string $rulesFilePath): array;

    public function filterDataObject(object $data, string $rulesFilePath): object;

    public function filterRequestData(array $data, string $rulesFilePath): array;

    public function filterResponseData(array $data, string $rulesFilePath): object;
}
