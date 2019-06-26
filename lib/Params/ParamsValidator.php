<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\ParamsException;
use Params\FirstRule\FirstRule;
use Params\SubsequentRule\SubsequentRule;
use Params\ValidationErrors;
use VarMap\VarMap;

/**
 * Class ParamsValidator
 *
 * Validates an input parameter according to a set of rules.
 * If there are any errors, they will be stored in this object,
 * and can be retrieved via the method ParamsValidator::getValidationProblems
 */
class ParamsValidator
{
    /**
     * @var string[]
     */
    private $validationProblems = [];

    private $paramValues = [];

    public function __construct()
    {
        $this->validationProblems = [];
    }

    /**
     * Gets the currently processed params.
     * @return array
     */
    public function getParamsValues()
    {
        return $this->paramValues;
    }

    public function hasParam(string $name)
    {
        return array_key_exists($name, $this->paramValues);
    }

    public function getParam(string $name)
    {
        return $this->paramValues[$name];
    }


    public function validateRulesForParam(
        string $name,
        VarMap $varMap,
        FirstRule $firstRule,
        SubsequentRule ...$subsequentRules
    ) {
        $validationResult = $firstRule->process($name, $varMap, $this);

        if (($validationProblem = $validationResult->getProblemMessage()) != null) {
            $this->validationProblems[] = $validationProblem;
            return null;
        }

        $value = $validationResult->getValue();
        $this->paramValues[$name] = $value;
        if ($validationResult->isFinalResult() === true) {
            return $value;
        }

        foreach ($subsequentRules as $rule) {
            $validationResult = $rule->process($name, $value, $this);
            /** @var $validationResult \Params\ValidationResult */
            if (($validationProblem = $validationResult->getProblemMessage()) != null) {
                $this->validationProblems[] = $validationProblem;
                return null;
            }

            $value = $validationResult->getValue();
            $this->paramValues[$name] = $value;
            if ($validationResult->isFinalResult() === true) {
                break;
            }
        }

        return $value;
    }


    public function getValidationProblems(): ?ValidationErrors
    {
        if (count($this->validationProblems) !== 0) {
            return new ValidationErrors($this->validationProblems);
        }

        return null;
    }
}
