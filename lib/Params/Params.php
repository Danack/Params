<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\Exception\RulesEmptyException;
use Params\Exception\ParamsException;
use Params\FirstRule\FirstRule;

/**
 * Class Params
 *
 * Validates multiple parameters at once, each according to their
 * own set of rules.
 *
 * Any validation problem will cause a ValidationException to be thrown.
 *
 */
class Params
{
//    /**
//     * @param array $namedRules
//     * @return array
//     * @throws ValidationException
//     * @throws RulesEmptyException
//     */
//    public static function validate($namedRules)
//    {
//        $values = [];
//        $validationProblems = [];
//
//        foreach ($namedRules as $name => $rules) {
//            if (count($rules) === 0) {
//                throw new RulesEmptyException('Rules for validating ' . $name . ' are not set.');
//            }
//
//            $value = null;
//            foreach ($rules as $rule) {
//                $validationResult = $rule($name, $value);
//                /** @var $validationResult \Params\ValidationResult */
//                if (($validationProblem = $validationResult->getProblemMessage()) != null) {
//                    $validationProblems[] = $validationProblem;
//                    break;
//                }
//                $value = $validationResult->getValue();
//                if ($validationResult->isFinalResult() === true) {
//                    break;
//                }
//            }
//            $values[] = $value;
//        }
//
//        ValidationException::throwIfProblems("Validation problems", $validationProblems);
//
//        return $values;
//    }

    /**
     * @param string $classname
     * @param array $namedRules
     * @param VarMap|mixed $sourceData
     * @return object
     * @throws RulesEmptyException
     * @throws ValidationException
     */
    public static function create($classname, $namedRules, $sourceData)
    {
        $validator = self::executeRules($namedRules, $sourceData);
        $validationProblems = $validator->getValidationProblems();

        if ($validationProblems !== null) {
            throw new ValidationException("Validation problems", $validationProblems);
        }

        $reflection_class = new \ReflectionClass($classname);
        return $reflection_class->newInstanceArgs($validator->getParamsValues());
    }

    /**
     * @param $namedRules
     * @param $sourceData
     * @return ParamsValidator
     */
    public static function executeRules(
        $namedRules,
        $sourceData
    ) {
        $validator = new ParamsValidator();
        $params = [];
        foreach ($namedRules as $parameterName => $rules) {
            // TODO - test for packed array?

            if (count($rules) === 0) {
                throw new RulesEmptyException();
            }
            $firstRule = $rules[0];

            if (!$firstRule instanceof FirstRule) {
                throw ParamsException::badFirstRule();
            }

            $subsequentRules = array_splice($rules, 1);
            $params[] = $validator->validateRulesForParam(
                $parameterName,
                $sourceData,
                $firstRule,
                ...$subsequentRules
            );
        }

        return $validator;
    }

    /**
     * @param string $classname
     * @param array $namedRules
     * @return mixed -  [object|null, ValidationErrors|null]
     * @throws Exception\ParamsException
     * @throws ValidationException
     */
    public static function createOrError($classname, $namedRules, $sourceData)
    {
        $validator = self::executeRules($namedRules, $sourceData);
        $validationErrors = $validator->getValidationProblems();
        if ($validationErrors !== null) {
            return [null, $validationErrors];
        }

        $reflection_class = new \ReflectionClass($classname);
        return [$reflection_class->newInstanceArgs($validator->getParamsValues()), null];
    }
}
