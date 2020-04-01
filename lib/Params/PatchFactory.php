<?php

declare(strict_types=1);

namespace Params;

use Params\PatchOperation\AddPatchOperation;
use Params\PatchOperation\CopyPatchOperation;
use Params\PatchOperation\MovePatchOperation;
use Params\PatchOperation\PatchOperation;
use Params\PatchOperation\RemovePatchOperation;
use Params\PatchOperation\ReplacePatchOperation;
use Params\PatchOperation\TestPatchOperation;

class PatchFactory
{
    public static string $DATA_NOT_ARRAY_FOR_OPERATION = "Not a valid operation object.";

    public static string $ADD_OP_MUST_CONTAIN_VALUE = "Add operation must contain an entry for 'value'";
    public static string $COPY_OP_MUST_CONTAIN_VALUE = "Copy operation must contain an entry for 'from'";
    public static string $MOVE_OP_MUST_CONTAIN_VALUE = "Move operation must contain an entry for 'from'";
    public static string $REPLACE_OP_MUST_CONTAIN_VALUE = "Replace operation must contain an entry for 'value'";
    public static string $TEST_OP_MUST_CONTAIN_VALUE = "Test operation must contain an entry for 'value'";

    public static string $UNKNOWN_OPERATION = "Unknown operation '%s'";

    public static string $OPERATION_MISSING_OP = "All operations must contain entry for 'op'";
    public static string $OPERATION_MISSING_PATH = "All operations must contain entry for 'path'";

    /**
     * @param string $op
     * @param string $path
     * @param array $patchEntryInput
     * @return array{?string, ?PatchOperation}
     */
    private static function createPatchEntryForArray(
        string $op,
        string $path,
        array $patchEntryInput
    ) {
        if ($op === PatchOperation::TEST) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return [self::$TEST_OP_MUST_CONTAIN_VALUE, null];
            }
            return [null, new TestPatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::REMOVE) {
            return [null, new RemovePatchOperation($path)];
        }
        if ($op === PatchOperation::ADD) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return [self::$ADD_OP_MUST_CONTAIN_VALUE, null];
            }
            return [null, new AddPatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::REPLACE) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return [self::$REPLACE_OP_MUST_CONTAIN_VALUE, null];
            }
            return [null, new ReplacePatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::MOVE) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return [self::$MOVE_OP_MUST_CONTAIN_VALUE, null];
            }
            return [null, new MovePatchOperation($path, $patchEntryInput['from'])];
        }
        if ($op === PatchOperation::COPY) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return [self::$COPY_OP_MUST_CONTAIN_VALUE, null];
            }
            return [null, new CopyPatchOperation($path, $patchEntryInput['from'])];
        }

        $message = sprintf(self::$UNKNOWN_OPERATION, $op);

        return [$message, null];
    }


    /**
     * Converts input data into patch objects or gives error.
     *
     * @param array $patchEntry
     * @return array{0:PatchObjectProblem|null, 1:PatchOperation|null}
     *
     */
    private static function convertInputArrayToPatchObjects(
        int $index,
        array $patchEntry
    ) {
        if (array_key_exists('op', $patchEntry) === false) {
            return [
                new PatchObjectProblem($index, self::$OPERATION_MISSING_OP),
                null
            ];
        }
        if (array_key_exists('path', $patchEntry) === false) {
            return [
                new PatchObjectProblem($index, self::$OPERATION_MISSING_PATH),
                null
            ];
        }

        [$patchErrorString, $patchOperation] = self::createPatchEntryForArray(
            $patchEntry['op'],
            $patchEntry['path'],
            $patchEntry
        );

        if ($patchErrorString !== null) {
            return [new PatchObjectProblem($index, $patchErrorString), null];
        }

        return [null, $patchOperation];
    }


    /**
     * @param array $value
     */
    public static function convertInputToPatchObjects(array $value): PatchValidationResult
    {
        /** @var \Params\PatchObjectProblem[] $validationProblems */
        $validationProblems = [];

        /** @var PatchOperation[] $patchOperations */
        $patchOperations = [];
        $count = 0;

        foreach ($value as $patchEntryInput) {
            $error = null;
            $patchOperation = null;
            // todo - do we want to support both array and object decoded patches?
            // for now we will.
            if (is_array($patchEntryInput) !== true) {
                $validationProblems[] = new PatchObjectProblem(
                    $count,
                    self::$DATA_NOT_ARRAY_FOR_OPERATION,
                );
                continue;
            }

            [$newValidationProblem, $patchOperation] = self::convertInputArrayToPatchObjects(
                $count,
                $patchEntryInput
            );

            if ($newValidationProblem !== null) {
                $validationProblems[] = $newValidationProblem;
            }

            if ($patchOperation !== null) {
                $patchOperations[] = $patchOperation;
            }

            $count++;
        }

        if (count($validationProblems) > 0) {
            return PatchValidationResult::thisIsMultipleErrorResult(
                $validationProblems,
                $patchOperations
            );
        }

        return PatchValidationResult::valueResult($patchOperations);
    }
}
