<?php

declare(strict_types = 1);

namespace Params\Exception;

use Params\ValidationErrors;

class ValidationException extends \Params\Exception\ParamsException
{
    /** @var ValidationErrors  */
    private $validationProblems;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param ValidationErrors $validationProblems
     * @param \Exception|null $previous
     */
    public function __construct($message, ValidationErrors $validationProblems, \Exception $previous = null)
    {
        $actualMessage = $message . " ";
        $actualMessage .= implode(", ", $validationProblems->getValidationProblems());

        $this->validationProblems = $validationProblems;

        parent::__construct($actualMessage, $code = 0, $previous);
    }

    public function getValidationProblems(): ValidationErrors
    {
        return $this->validationProblems;
    }
}
