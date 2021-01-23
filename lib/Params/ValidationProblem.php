<?php

declare(strict_types = 1);

namespace Params;

use Params\InputStorage\InputStorage;

class ValidationProblem
{
    /**
     * The inputStorage in the state when the parameter that has a validation
     * problem was being validated.
     */
    private InputStorage $inputStorage;

    /**
     * A text description of the problem.
     */
    private string $problemMessage;

    public function __construct(InputStorage $dataLocator, string $description)
    {
        $this->inputStorage = $dataLocator;
        $this->problemMessage = $description;
    }

    public function getInputStorage(): InputStorage
    {
        return $this->inputStorage;
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
        return $this->inputStorage->getPath() . " " . $this->problemMessage;
    }
}
