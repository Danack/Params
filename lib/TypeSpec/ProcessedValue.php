<?php

declare(strict_types = 1);

namespace TypeSpec;

/**
 *
 */
class ProcessedValue
{
    public function __construct(
        private InputTypeSpec $inputTypeSpec,
        private mixed $value
    ) {
    }

    public function getInputTypeSpec(): InputTypeSpec
    {
        return $this->inputTypeSpec;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
