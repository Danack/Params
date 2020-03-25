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
    /** @var array<string, mixed>  */
    private array $paramValues = [];

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
     * @param Path $path
     * @param ProcessRule ...$subsequentRules
     * @throws Exception\ParamMissingException
     * @return \Params\ValidationProblem[]
     */
    public function validateSubsequentRules(
        $value,
        Path $path,
        ProcessRule ...$subsequentRules
    ) {
        foreach ($subsequentRules as $rule) {
            $validationResult = $rule->process($path, $value, $this);
            if ($validationResult->anyErrorsFound()) {
                return $validationResult->getValidationProblems();
            }

            $value = $validationResult->getValue();
            $this->paramValues[$path->getCurrentName()] = $value;
            if ($validationResult->isFinalResult() === true) {
                break;
            }
        }

        return [];
    }

    /**
     * @param \Params\Param $param
     * @param VarMap $varMap
     * @param Path $path
     * @return array|ValidationProblem[]
     * @throws Exception\ParamMissingException
     */
    public function validateParam(
        Param $param,
        VarMap $varMap,
        Path $path
    ) {
        $pathForParam = $path->addNamePathFragment($param->getInputName());

        $firstRule = $param->getFirstRule();
        $subsequentRules = $param->getSubsequentRules();

        $validationResult = $firstRule->process($pathForParam, $varMap, $this);

        if ($validationResult->anyErrorsFound()) {
            return $validationResult->getValidationProblems();
        }

        $value = $validationResult->getValue();
        $this->paramValues[$pathForParam->getCurrentName()] = $value;
        if ($validationResult->isFinalResult() === true) {
            return [];
        }

        return $this->validateSubsequentRules(
            $value,
            $pathForParam,
            ...$subsequentRules
        );
    }


    /**
     * @param \Params\Param[] $params
     * @param VarMap $sourceData
     * @return \Params\ValidationProblem[]
     */
    public function executeRulesWithValidator(
        $params,
        VarMap $sourceData
    ) {
        $validationProblems = [];
        $path = new Path();

        foreach ($params as $param) {
            $newValidationProblems = $this->validateParam(
                $param,
                $sourceData,
                $path
            );

            $validationProblems = [...$validationProblems, ...$newValidationProblems];
        }

        return $validationProblems;
    }
}
