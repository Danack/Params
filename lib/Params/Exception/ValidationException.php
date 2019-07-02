<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\ValidationErrors;

class ValidationException extends \Params\Exception\ParamsException
{
    /** @var array */
    private $validationProblems;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param string[] $validationProblems
     * @param \Exception|null $previous
     */
    public function __construct(string $message, array $validationProblems, \Exception $previous = null)
    {
        $actualMessage = $message . " ";


        $actualMessage .= implode(", ", $validationProblems);

        $this->validationProblems = $validationProblems;

        parent::__construct($actualMessage, $code = 0, $previous);
    }

    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }
}
