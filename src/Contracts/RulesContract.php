<?php

namespace YoungPandas\DataFilter\Contracts;

/**
 * Rules Contract
 * @package YoungPandas\DataFilter\Contracts
 */
interface RulesContract
{
    /**
     * @param array $data
     * @param string $rulesFilePath
     * @param string $prefix
     * @return array
     */
    public function applyRules(array $data, string $rulesFilePath, string $prefix): array;
}
