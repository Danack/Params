<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\PatchOperation\PatchOperation;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\Exception\PatchFormatException;
use Params\Path;

/**
 * Class Params
 *
 * Validates multiple parameters at once, each according to their
 * own set of rules.
 *
 * Any validation problem will cause a ValidationException to be thrown.
 *
 */
class ParamsExecutor
{


//    /**
//     * @param \Params\Param[] $rulesetList
//     * @param VarMap $sourceData
//     * @return {\Params\ParamsValuesImpl, \Params\ValidationProblem[]}
//     */
//    public static function executeRules($rulesetList, $sourceData)
//    {
//        $paramsValuesImpl = new ParamsValuesImpl();
//        $validationProblems = $paramsValuesImpl->executeRulesWithValidator($rulesetList, $sourceData);
//
//        return [$paramsValuesImpl, $validationProblems];
//    }


    /**
     * @template T
     * @param class-string<T> $classname
     * @param \Params\Param[] $rulesetList
     * @return {object, \Params\ValidationProblem[]}|{null, \Params\ValidationProblem[]}
     * @throws Exception\ParamsException
     * @throws ValidationException
     *
     * The rules are passed separately to the classname so that we can
     * support rules coming both from static info and from factory objects.
     */
    public static function createOrError($classname, $rulesetList, VarMap $sourceData)
    {
//        [$paramsValuesImpl, $validationProblems] = self::executeRules($rulesetList, $sourceData);
        $paramsValuesImpl = new ParamsValuesImpl();
        $validationProblems = $paramsValuesImpl->executeRulesWithValidator($rulesetList, $sourceData);

//        $validationProblems = $validator->getValidationProblems();
        if (count($validationProblems) !== 0) {
            return [null, $validationProblems];
        }

        $reflection_class = new \ReflectionClass($classname);
        return [$reflection_class->newInstanceArgs($paramsValuesImpl->getParamsValues()), []];
    }



    /**
     * @template T
     * @param class-string<T> $classname
     * @param \Params\Param[] $namedRules
     * @param VarMap $sourceData
     * @return T of object
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public static function create($classname, $namedRules, VarMap $sourceData)
    {
        $paramsValuesImpl = new ParamsValuesImpl();

        $validationProblems = $paramsValuesImpl->executeRulesWithValidator($namedRules, $sourceData);

        if (count($validationProblems) !== 0) {
            throw new ValidationException("Validation problems", $validationProblems);
        }

        $reflection_class = new \ReflectionClass($classname);

        $object = $reflection_class->newInstanceArgs($paramsValuesImpl->getParamsValues());

        /** @var T $object */
        return $object;
    }

    /**
     * Creating patches is slightly harder. For Params the order of parameters isn't
     * important, for patch operations it is. e.g. copy -> delete != delete->copy
     *
     */
    public static function createPatch($namedRules, array $sourceData): array
    {
        [$operations, $validationProblems] = self::processOperations($namedRules, $sourceData);

        if (count($validationProblems) !== 0) {
            throw new ValidationException(
                "Validation problems",
                $validationProblems
            );
        }

        return $operations;
    }

    /**
     * @param string $path
     * @param string $pathRegex
     * @return array{0: bool, 1: string}
     */
    public static function pathMatches(string $path, string $pathRegex)
    {
        // TODO - we need to return bool $isMatch, and array $namedParams
        return [true, $path];
    }

    public static function processPatchObject(
        PatchOperation $patchObject,
        $patchRules
    ) {
        foreach ($patchRules as $patchRule) {
            /** @var \Params\PatchRule\PatchRule $patchRule */
            if ($patchObject->getOpType() !== $patchRule->getOpType()) {
                continue;
            }

            [$pathMatch, $params] = self::pathMatches(
                $patchObject->getPath(),
                $patchRule->getPathRegex()
            );

            if ($pathMatch !== true) {
                continue;
            }

            // TODO - pass in $params here also
            return self::createOrError(
                $patchRule->getClassName(),
                $patchRule->getRules(),
                new ArrayVarMap($patchObject->getValue())
            );
        }

        $message = sprintf(
            "Failed to match path '%s' for op type '%s'",
            $patchObject->getPath(),
            $patchObject->getOpType()
        );

        return [null, [$message]];
    }

    /**
     * @param $patchRules
     * @param array $sourceData
     * @return array
     */
    public static function processOperations(
        $patchRules,
        array $sourceData
    ) {
        $validationResult = PatchFactory::convertInputToPatchObjects($sourceData);

        if ($validationResult->anyErrorsFound()) {
            throw new PatchFormatException(
                "Patch format error: " . implode(",", $validationResult->getValidationProblems())
            );
        }

        $patchObjects = $validationResult->getValue();

        $operationObjects = [];
        $allProblems = [];

        foreach ($patchObjects as $patchObject) {
            [$operationObject, $problems] = self::processPatchObject($patchObject, $patchRules);

            if ($operationObject !== null) {
                $operationObjects[] = $operationObject;
            }
            if (count($problems) !== 0) {
                array_push($allProblems, ...$problems);
            }
        }

        if (count($allProblems) !== 0) {
            return [null, $allProblems];
        }

        return [$operationObjects, []];
    }
}
