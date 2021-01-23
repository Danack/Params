<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\LogicException;

/**
 * A class to stores the processed parameters, so that they can be accessed by subsequent rules.
 *
 * This is useful for when you want to have a rule that one parameter must be a
 * duplicate of another parameter. e.g. email double-entry
 */
class ProcessedValues
{
    /** @var array<int|string, mixed>  */
    private array $paramValues = [];

    /**
     * @param array $values
     * @return self
     * @throws LogicException
     */
    public static function fromArray(array $values): self
    {
        foreach ($values as $key => $value) {
            if (is_string($key) !== true) {
                throw LogicException::keysMustBeStrings();
            }
        }

        $instance = new self();
        $instance->paramValues = $values;

        return $instance;
    }

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
     * @param string $name
     * @return mixed
     */
    public function getValue($name)
    {
        if (array_key_exists($name, $this->paramValues) === false) {
            throw LogicException::missingValue($name);
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
