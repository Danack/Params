<?php

declare(strict_types = 1);

namespace Params;

use Params\Exception\ValidationException;
use Params\PatchOperation\PatchOperation;
use Params\Path;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\Exception\PatchFormatException;

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
    /**
     * @template T
     * @param class-string<T> $classname
     * @param \Params\Param[] $params
     * @return array{0:?object, 1:\Params\ValidationProblem[]}
     * @throws Exception\ParamsException
     * @throws ValidationException
     *
     * The rules are passed separately to the classname so that we can
     * support rules coming both from static info and from factory objects.
     */
    public static function createOrError($classname, $params, VarMap $sourceData)
    {
        $paramsValuesImpl = new ParamsValuesImpl();
        $path = Path::initial();
        $validationProblems = $paramsValuesImpl->executeRulesWithValidator($params, $sourceData, $path);

        if (count($validationProblems) !== 0) {
            return [null, $validationProblems];
        }

        $reflection_class = new \ReflectionClass($classname);

        // TODO - wrap this in an ResultObject.

        return [$reflection_class->newInstanceArgs($paramsValuesImpl->getParamsValues()), []];
    }


    /**
     * @template T
     * @param class-string<T> $classname
     * @param \Params\Param[] $params
     * @param Path $path
     * @return array{0:?object, 1:\Params\ValidationProblem[]}
     * @throws Exception\ParamsException
     * @throws ValidationException
     *
     * The rules are passed separately to the classname so that we can
     * support rules coming both from static info and from factory objects.
     */
    public static function createOrErrorFromPath($classname, $params, VarMap $sourceData, Path $path)
    {
        $paramsValuesImpl = new ParamsValuesImpl();
        $validationProblems = $paramsValuesImpl->executeRulesWithValidator($params, $sourceData, $path);

        if (count($validationProblems) !== 0) {
            return [null, $validationProblems];
        }

        $reflection_class = new \ReflectionClass($classname);
        return [$reflection_class->newInstanceArgs($paramsValuesImpl->getParamsValues()), []];
    }

    /**
     * @template T
     * @param class-string<T> $classname
     * @param \Params\Param[] $params
     * @param VarMap $sourceData
     * @return T of object
     * @throws ValidationException
     * @throws \ReflectionException
     */
    public static function create($classname, $params, VarMap $sourceData)
    {
        $paramsValuesImpl = new ParamsValuesImpl();
        $path = Path::initial();

        $validationProblems = $paramsValuesImpl->executeRulesWithValidator(
            $params,
            $sourceData,
            $path
        );

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
     * @param \Params\PatchRule\PatchRule[] $namedRules
     * @param array $sourceData
     * @return array
     * @throws PatchFormatException
     * @throws ValidationException
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

    /**
     * @param PatchOperation $patchObject
     * @param \Params\PatchRule\PatchRule[] $patchRules
     * @return array
     * @throws Exception\ParamsException
     * @throws ValidationException
     */
    public static function applyRulesToPatchOperation(
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

        // TODO - This is a bug. Message shouldn't be a string but a ValidationProblem
        return [null, [$message]];
    }

    /**
     * @param \Params\PatchRule\PatchRule[] $patchRules
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
                "Patch format error: " . implode(",", $validationResult->getPatchObjectProblems())
            );
        }

        $patchOperations = $validationResult->getPatchOperations();

        $operationObjects = [];
        $allProblems = [];

        foreach ($patchOperations as $patchOperation) {
            [$operationObject, $problems] = self::applyRulesToPatchOperation($patchOperation, $patchRules);

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
