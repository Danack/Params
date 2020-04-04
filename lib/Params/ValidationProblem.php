<?php

declare(strict_types = 1);

namespace Params;

use Params\DataLocator\DataLocator;

class ValidationProblem
{
    /**
     * The path of the parameter that was being validated.
     */
    private DataLocator $dataLocator;

    private string $problemMessage;

    public function __construct(DataLocator $dataLocator, string $description)
    {
        $this->dataLocator = $dataLocator;
        $this->problemMessage = $description;
    }

    public function getDataLocator(): DataLocator
    {
        return $this->dataLocator;
    }

    /**
     * @return string
     */
    public function getProblemMessage(): string
    {
        return $this->problemMessage;
    }

    /**
     * TODO - this probably shouldn't be here?
     * Need to think about where message formatting should be done.
     */
    public function toString(): string
    {
        return $this->dataLocator->toString() . " " . $this->problemMessage;
    }
}
