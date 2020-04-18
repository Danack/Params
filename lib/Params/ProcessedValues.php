<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\LogicException;

/**
 * stores the processed values, so that they can be accessed by subsequent parameters.
 *
 */
class ProcessedValues
{
    /** @var array<int|string, mixed>  */
    private array $paramValues = [];


    /**
     * Gets the currently processed params.
     * @return array<int|string, mixed>
     */
    public function getAllValues()
    {
        return $this->paramValues;
    }

    /**
     * @param string|int $name
     */
    public function hasValue($name): bool
    {
        return array_key_exists($name, $this->paramValues);
    }

    /**
     * @param string|int $name
     * @return mixed
     */
    public function getValue($name)
    {
        if (array_key_exists($name, $this->paramValues) === false) {
            throw new LogicException("Trying to access $name which isn't present in ParamValuesImpl.");
        }

        return $this->paramValues[$name];
    }


    /**
     * @param string|int $name
     * @param mixed $value
     */
    public function setValue($name, $value): void
    {
        $this->paramValues[$name] = $value;
    }
}
