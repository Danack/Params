<?php

declare(strict_types = 1);

namespace Params;

use Params\FirstRule\FirstRule;
use Params\SubsequentRule\SubsequentRule;
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

    /**
     * @param mixed $value
     * @param string $name The name is used to store values, and to generate appropriate error messages.
     * @param SubsequentRule ...$subsequentRules
     * @throws Exception\ParamMissingException
     */
    public function validateSubsequentRules(
        $value,
        string $name,
        SubsequentRule ...$subsequentRules
    ) {
        foreach ($subsequentRules as $rule) {
            $validationResult = $rule->process($name, $value, $this);
            $validationProblems = $validationResult->getProblemMessages();
            if (count($validationProblems) != 0) {
                foreach ($validationProblems as $path => $validationProblem) {
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
        FirstRule $firstRule,
        SubsequentRule ...$subsequentRules
    ) {
        $validationResult = $firstRule->process($name, $varMap, $this);
        $validationProblems = $validationResult->getProblemMessages();
        if (count($validationProblems) !== 0) {
//            array_push($this->validationProblems, ...$validationProblems);
//            $this->validationProblems = Functions::addChildErrorMessagesForParam(
//                $name,
//                $validationProblems,
//                $this->validationProblems
//            );

            foreach ($validationProblems as $key => $value) {
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
