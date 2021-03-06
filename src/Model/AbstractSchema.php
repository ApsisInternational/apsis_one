<?php

namespace Apsis\One\Model;

abstract class AbstractSchema implements SchemaInterface
{
    /**
     * @var array
     */
    protected $definition = [];

    /**
     * @var array
     */
    protected $definitionTypes = [];

    /**
     * {@inheritdoc}
     */
    abstract public function __construct();

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        return $this->definition;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionTypes(): array
    {
        return $this->definitionTypes;
    }
}
