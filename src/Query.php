<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/4/18
 * Time: 11:56 PM
 */

namespace GraphQL;

/**
 * Class Query
 *
 * @package GraphQL
 */
class Query
{
    /**
     * Stores the GraphQL query format
     *
     * @var string
     */
    private static $queryFormat = "%s%s {\n%s\n}";

    /**
     * Stores the object being queried for
     *
     * @var string
     */
    private $object;

    /**
     * Stores the list of arguments used when querying data
     *
     * @var array
     */
    private $arguments;

    /**
     * Stores the selection set desired to get from the query, can include nested queries
     *
     * @var array
     */
    private $selectionSet;

    /**
     * Private member that's not accessible from outside the class, used internally to deduce if query is nested or not
     *
     * @var bool
     */
    private $isNested;

    /**
     * GQLQueryBuilder constructor.
     *
     * @param string $queryObject
     */
    public function __construct($queryObject)
    {
        $this->object       = $queryObject;
        $this->arguments    = [];
        $this->selectionSet = [];
        $this->isNested     = false;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function constructArguments()
    {
        // Return empty string if list is empty
        if (empty($this->arguments)) {
            return '';
        }

        // Construct arguments string if list not empty
        $constraintsString = '(';
        $first             = true;
        foreach ($this->arguments as $constraint => $value) {

            // Append space at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $constraintsString .= ' ';
            }

            // Wrap the value with quotations if it's a string value
            if (is_string($value)) {
                if ($value[0] != '"') {
                    $value = '"' . $value;
                }
                if (substr($value, -1) != '"') {
                    $value .= '"';
                }
            }
            $constraintsString .= $constraint . ': ' . $value;
        }
        $constraintsString .= ')';

        return $constraintsString;
    }

    /**
     * @return string
     */
    protected function constructSelectionSet()
    {
        $attributesString = '';
        $first            = true;
        foreach ($this->selectionSet as $attribute) {

            // Append empty line at the beginning if it's not the first item on the list
            if ($first) {
                $first = false;
            } else {
                $attributesString .= "\n";
            }

            // If query is included in attributes set as a nested query
            if ($attribute instanceof Query) {
                $attribute->setAsNested();
            }

            // Append attribute to returned attributes list
            $attributesString .= $attribute;
        }

        return $attributesString;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        $queryFormat = static::$queryFormat;
        if (!$this->isNested) {
            $queryFormat = "query {\n" . static::$queryFormat . "\n}";
        }
        $argumentsString    = $this->constructArguments();
        $selectionSetString = $this->constructSelectionSet();

        return sprintf($queryFormat, $this->object, $argumentsString, $selectionSetString);
    }

    /**
     *
     */
    private function setAsNested()
    {
        $this->isNested = true;
    }

    /**
     * @param array $arguments
     *
     * @return Query
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @param array $selectionSet
     *
     * @return Query
     */
    public function setSelectionSet(array $selectionSet)
    {
        $this->selectionSet = $selectionSet;

        return $this;
    }
}
