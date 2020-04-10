<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\LogicException;
use Params\ProcessRule\ProcessRule;
use VarMap\VarMap;
use Params\DataLocator\InputStorageAye;

/**
 * Class ParamsValidator
 *
 * Validates an input parameter according to a set of rules.
 * If there are any errors, they will be stored in this object,
 * and can be retrieved via the method ParamsValidator::getValidationProblems
 *
 * This is inadequate. We should support full paths and relative paths
 * so that people can validate across objects, and also within arrays.
 */
class ProcessedValuesImpl implements ProcessedValues
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
