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
    private static function createPatchEntryForArray($op, $path, $patchEntryInput)
    {
        if ($op === PatchOperation::TEST) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Test operation must contain an entry for 'value'", null];
            }
            return [null, new TestPatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::REMOVE) {
            return [null, new RemovePatchOperation($path)];
        }
        if ($op === PatchOperation::ADD) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Add operation must contain an entry for 'value'", null];
            }
            return [null, new AddPatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::REPLACE) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Replace operation must contain an entry for 'value'", null];
            }
            return [null, new ReplacePatchOperation($path, $patchEntryInput['value'])];
        }
        if ($op === PatchOperation::MOVE) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Move operation must contain an entry for 'from'", null];
            }
            return [null, new MovePatchOperation($path, $patchEntryInput['from'])];
        }
        if ($op === PatchOperation::COPY) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Copy operation must contain an entry for 'from'", null];
            }
            return [null, new CopyPatchOperation($path, $patchEntryInput['from'])];
        }

        return ["Unknown operation '$op'", null];
    }


    /**
     * @param array $patchEntry
     * @return array
     */
    private static function checkArrayEntryForValidity(array $patchEntry)
    {
        if (array_key_exists('op', $patchEntry) === false) {
            return ["missing 'op'", null];
        }
        if (array_key_exists('path', $patchEntry) === false) {
            return ["missing 'path'", null];
        }

        return self::createPatchEntryForArray(
            $patchEntry['op'],
            $patchEntry['path'],
            $patchEntry
        );
    }

    /**
     * @param array $value
     * @return \Params\ValidationResult
     */
    public static function convertInputToPatchObjects($value)
    {
        $errorMessages = [];
        $patchEntries = [];

        $count = 0;
        foreach ($value as $patchEntryInput) {
            $error = null;
            $patchEntry = null;

            // todo - do we want to support both array and object decoded patches?
            // for now we will.
            if (is_array($patchEntryInput) === true) {
                [$error, $patchEntry] = self::checkArrayEntryForValidity($patchEntryInput);
            }
            else {
                $error = "Patch entry $count is not an array.";
            }

            if ($error !== null) {
                $errorMessages[] = "Error for entry $count: " . $error;
            }
            if ($patchEntry !== null) {
                $patchEntries[] = $patchEntry;
            }

            $count++;
        }

        if (count($errorMessages) > 0) {
            $message = sprintf(
                'Data for patching is invalid: %s',
                implode(', ', $errorMessages)
            );

            // TODO - is this root name correct?
            return ValidationResult::errorResult("", $message);
        }

        return ValidationResult::valueResult($patchEntries);
    }
}
