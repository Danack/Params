<?php

declare(strict_types = 1);

namespace Params;

use Params\InputParameter;

class ProcessedValue
{
    public function __construct(
        private \Params\InputParameter $param,
        private mixed $value
    ) {
    }

    public function getParam(): \Params\InputParameter
    {
        return $this->param;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
