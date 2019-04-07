<?php

namespace GraphQL\SchemaObject;

use GraphQL\Exception\EmptySelectionSetException;
use GraphQL\QueryBuilder\AbstractQueryBuilder;

/**
 * An abstract class that acts as the base for all schema query objects generated by the SchemaClassGenerator
 *
 * Class QueryObject
 *
 * @package GraphQL\SchemaObject
 */
abstract class QueryObject extends AbstractQueryBuilder
{
    /**
     * This constant stores the name to be given to the root query object
     *
     * @var  string
     */
    public const ROOT_QUERY_OBJECT_NAME = 'Root';

    /**
     * This constant stores the name of the object name in the API definition
     *
     * @var string
     */
    protected const OBJECT_NAME = '';

    /**
     * SchemaObject constructor.
     *
     * @param string $fieldName
     */
    public function __construct(string $fieldName = '')
    {
        $queryObject = !empty($fieldName) ? $fieldName : static::OBJECT_NAME;
        parent::__construct($queryObject);
    }

    /**
     * @param array $arguments
     *
     * @return $this
     */
    protected function appendArguments(array $arguments): QueryObject
    {
        foreach ($arguments as $argName => $argValue) {
            if ($argValue instanceof InputObject) {
                $argValue = $argValue->toRawObject();
            }

            $this->setArgument($argName, $argValue);
        }

        return $this;
    }

    /**
     * @return string
     * @throws EmptySelectionSetException
     */
    public function getQueryString(): string
    {
        return (string) $this->getQuery();
    }
}