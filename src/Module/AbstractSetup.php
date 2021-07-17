<?php

namespace Apsis\One\Module;

use Apsis\One\Module\Configuration\Configs;
use Apsis_one;

abstract class AbstractSetup implements SetupInterface
{
    /**
     * @var Configs
     */
    protected $configs;

    /**
     * @var Apsis_one
     */
    protected $module;

    /**
     * AbstractSetup constructor.
     *
     * @param Configs $configs
     */
    public function __construct(Configs $configs)
    {
        $this->configs = $configs;
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function getTableWithDbPrefix(string $tableName): string
    {
        return _DB_PREFIX_ . $tableName;
    }

    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return string
     */
    protected function getIndex(string $tableName, string $columnName): string
    {
        return 'IDX_' . strtoupper($tableName . '_' . $columnName);
    }
}