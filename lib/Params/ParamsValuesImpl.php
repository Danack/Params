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
class ParamsValuesImpl implements ParamValues
{
//    /**
//     * @var \Params\ValidationProblem[]
//     */
//    private array $validationProblems = [];

    /** @var array<string, mixed>  */
    private array $paramValues = [];

//    public function __construct()
//    {
//        $this->validationProblems = [];
//    }

    /**
     * Gets the currently processed params.
     * @return array<string, mixed>
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
     * @return \Params\ValidationProblem[]
     */
    public function validateSubsequentRules(
        $value,
        string $name,
        ProcessRule ...$subsequentRules
    ) {
        foreach ($subsequentRules as $rule) {
            $validationResult = $rule->process($name, $value, $this);
            if ($validationResult->anyErrorsFound()) {
//                $this->validationProblems = [$this->validationProblems, ]
                return $validationResult->getValidationProblems();
            }

            $value = $validationResult->getValue();
            $this->paramValues[$name] = $value;
            if ($validationResult->isFinalResult() === true) {
                break;
            }
        }

//        $this->paramValues[$name] = $value;
        // Return no problems
        return [];
    }

    /**
     * @param string $name
     * @param VarMap $varMap
     * @param ExtractRule $firstRule
     * @param ProcessRule ...$subsequentRules
     * @return \Params\ValidationProblem[]
     * @throws Exception\ParamMissingException
     */
    public function validateRulesForParam(
        string $name,
        VarMap $varMap,
        ExtractRule $firstRule,
        ProcessRule ...$subsequentRules
    ) {
        $validationResult = $firstRule->process($name, $varMap, $this);

        if ($validationResult->anyErrorsFound()) {
            return $validationResult->getValidationProblems();
//            $this->validationProblems = [...$this->validationProblems, ...$validationResult->getValidationProblems()];
//            return null;
        }

        $value = $validationResult->getValue();
        $this->paramValues[$name] = $value;
        if ($validationResult->isFinalResult() === true) {
            return [];
        }

        return $this->validateSubsequentRules(
            $value,
            $name,
            ...$subsequentRules
        );
    }

//    /**
//     * @return \Params\ValidationProblem[]
//     */
//    public function getValidationProblems(): array
//    {
//        return $this->validationProblems;
//    }

    /**
     * @param \Params\Param[] $rulesetList
     * @param VarMap $sourceData
     * @return \Params\ValidationProblem[]
     */
    public function executeRulesWithValidator(
        $rulesetList,
        VarMap $sourceData
    ) {
        $validationProblems = [];

        foreach ($rulesetList as $ruleset) {

            // Path add name fragment '$ruleset->getInputName()'
            $newValidationProblems = $this->validateRulesForParam(
                $ruleset->getInputName(),
                $sourceData,
                $ruleset->getFirstRule(),
                ...$ruleset->getSubsequentRules()
            );

            $validationProblems = [...$validationProblems, ...$newValidationProblems];
        }

        return $validationProblems;
    }
}
