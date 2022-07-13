<?php

declare(strict_types = 1);

namespace TypeSpec;

use TypeSpec\InputTypeSpec;

class ProcessedValue
{
    public function __construct(
        private \TypeSpec\InputTypeSpec $param,
        private mixed                    $value
    ) {
    }

    public function getParam(): \TypeSpec\InputTypeSpec
    {
        return $this->param;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
