<?php

declare(strict_types = 1);

namespace TypeSpec;

use TypeSpec\Exception\LogicException;
use TypeSpec\InputTypeSpec;

/**
 * A class to stores the processed parameters, so that they can be accessed by subsequent rules.
 *
 * This is useful for when you want to have a rule that one parameter must be a
 * duplicate of another parameter. e.g. email double-entry
 */
class ProcessedValues
{
    /** @var ProcessedValue[]  */
    private array $processedValues = [];

    /**
     * @param ProcessedValue[] $processedValues
     * @return self
     * @throws LogicException
     */
    public static function fromArray(array $processedValues): self
    {
        foreach ($processedValues as $processedValue) {
            /** @psalm-suppress DocblockTypeContradiction */
            if (!($processedValue instanceof ProcessedValue)) {
                throw new LogicException("Processed values must all be instances of ProcessedValue.");
            }
        }

        $instance = new self();
        $instance->processedValues = $processedValues;

        return $instance;
    }

    /**
     * TODO - is this required?
     * Gets the currently processed params.
     * @return array<int|string, mixed>
     */
    public function getAllValues()
    {
        $values = [];
        foreach ($this->processedValues as $processedValue) {
            $values[$processedValue->getParam()->getInputName()] = $processedValue->getValue();
        }

        return $values;
    }

    public function getCount(): int
    {
        return count($this->processedValues);
    }

    /**
     * @return ProcessedValue[]
     */
    public function getProcessedValues(): array
    {
        return $this->processedValues;
    }

    /**
     * @param string|int $name
     */
    public function hasValue($name): bool
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getParam()->getInputName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getValue(string $name): mixed
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getParam()->getInputName()) {
                return $processedValue->getValue();
            }
        }
        throw LogicException::missingValue($name);
    }


    /**
     * @param InputTypeSpec $param
     * @param mixed $value
     */
    public function setValue(InputTypeSpec $param, mixed $value): void
    {
        $this->processedValues[] = new ProcessedValue($param, $value);
    }

    public function getValueForTargetParam(string $name): array
    {
        foreach ($this->processedValues as $processedValue) {
            if ($name === $processedValue->getParam()->getTargetParameterName()) {
                return [$processedValue->getValue(), true];
            }
        }

        return [null, false];
    }
}
