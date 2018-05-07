<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\ParamsException;

class ParamsValidator
{
    /**
     * @var array
     */
    private $validationProblems = [];

    public function __construct()
    {
        $this->validationProblems = [];
    }

    /**
     * @param $name string
     * @param $rules \Params\Rule[]
     * @return mixed
     * @throws ValidationException
     * @throws ParamsException
     */
    public function validate(string $name, array $rules)
    {
        if (count($rules) === 0) {
            throw new ParamsException('Rules for validating ' . $name . ' are not set.');
        }

        $value = null;
        foreach ($rules as $rule) {
            $validationResult = $rule($name, $value);
            /** @var $validationResult \Params\ValidationResult */
            if (($validationProblem = $validationResult->getProblemMessage()) != null) {
                $this->validationProblems[] = $validationProblem;
                return null;
            }
            if ($validationResult->isFinalResult() === true) {
                break;
            }
            $value = $validationResult->getValue();
        }

        return $value;
    }


    public function getValidationProblems()
    {
        return $this->validationProblems;
    }
}
