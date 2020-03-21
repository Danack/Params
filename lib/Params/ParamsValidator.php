<?php

declare(strict_types = 1);

namespace Params;

use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\ProcessRule;
use VarMap\VarMap;
use Params\Functions;

/**
 * Class ParamsValidator
 *
 * Validates an input parameter according to a set of rules.
 * If there are any errors, they will be stored in this object,
 * and can be retrieved via the method ParamsValidator::getValidationProblems
 */
class ParamsValidator implements ParamValues
{
    /**
     * @var string[]
     */
    private array $validationProblems = [];

    private array $paramValues = [];

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

    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->paramValues);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParam(string $name)
    {
        return $this->paramValues[$name];
    }

    /**
     * @param mixed $value
     * @param string $name The name is used to store values, and to generate appropriate error messages.
     * @param ProcessRule ...$subsequentRules
     * @throws Exception\ParamMissingException
     * @return {?mixed}
     */
    public function validateSubsequentRules(
        $value,
        string $name,
        ProcessRule ...$subsequentRules
    ) {
        foreach ($subsequentRules as $rule) {
            $validationResult = $rule->process($name, $value, $this);
            if ($validationResult->anyErrorsFound()) {
                foreach ($validationResult->getProblemMessages() as $path => $validationProblem) {
                    $this->validationProblems[$path] = $validationProblem;
                }
                return [null, $this->validationProblems];
            }

            $value = $validationResult->getValue();
            $this->paramValues[$name] = $value;
            if ($validationResult->isFinalResult() === true) {
                break;
            }
        }

        return [$value, []];
    }


    public function validateRulesForParam(
        string $name,
        VarMap $varMap,
        ExtractRule $firstRule,
        ProcessRule ...$subsequentRules
    ) {
        $validationResult = $firstRule->process($name, $varMap, $this);

        if ($validationResult->anyErrorsFound()) {
            foreach ($validationResult->getProblemMessages() as $key => $value) {
                $this->validationProblems[$key] = $value;
            }

            return null;
        }

        $value = $validationResult->getValue();
        $this->paramValues[$name] = $value;
        if ($validationResult->isFinalResult() === true) {
            return $value;
        }

        return $this->validateSubsequentRules(
            $value,
            $name,
            ...$subsequentRules
        );
    }

    /**
     * @return string[]
     */
    public function getValidationProblems(): array
    {
        return $this->validationProblems;
    }
}
