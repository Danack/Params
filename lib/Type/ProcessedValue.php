<?php

declare(strict_types = 1);

namespace Type;

use Type\PropertyDefinition;

class ProcessedValue
{
    public function __construct(
        private \Type\PropertyDefinition $param,
        private mixed                    $value
    ) {
    }

    public function getParam(): \Type\PropertyDefinition
    {
        return $this->param;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
