<?php

declare(strict_types=1);

namespace Params;

use Params\ValidationResult;
use Params\PatchOperation\AddPatchOperation;
use Params\PatchOperation\CopyPatchOperation;
use Params\PatchOperation\MovePatchOperation;
use Params\PatchOperation\PatchOperation;
use Params\PatchOperation\RemovePatchOperation;
use Params\PatchOperation\ReplacePatchOperation;
use Params\PatchOperation\TestPatchOperation;

class PatchFactory
{

//    /** @var string[] */
//    private $allowedOps;
//
//    public function __construct(Input $input, $allowedOps)
//    {
//        $this->input = $input;
//        $this->allowedOps = $allowedOps;
//    }

    private static function createPatchEntryForObject($op, $path, $patchEntryInput)
    {
//        if (in_array($op, $this->allowedOps, true) !== true) {
//            $message = sprintf(
//                "Op '%s' is not supported for this endpoint.",
//                $op
//            );
//            return [$message, null];
//        }

        if ($op === PatchOperation::TEST) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Test operation must contain an entry for 'value'", null];
            }
            return [null, new TestPatchOperation($path, $patchEntryInput->value)];
        }
        else if ($op === PatchOperation::REMOVE) {
            return [null, new RemovePatchOperation($path)];
        }
        else if ($op === PatchOperation::ADD) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Add operation must contain an entry for 'value'", null];
            }
            return [null, new AddPatchOperation($path, $patchEntryInput->value)];
        }
        else if ($op === PatchOperation::REPLACE) {
            if (property_exists($patchEntryInput, 'value') !== true) {
                return ["Replace operation must contain an entry for 'value'", null];
            }
            return [null, new ReplacePatchOperation($path, $patchEntryInput->value)];
        }
        else if ($op === PatchOperation::MOVE) {
            if (property_exists($patchEntryInput, 'from') !== true) {
                return ["Move operation must contain an entry for 'from'", null];
            }
            return [null, new MovePatchOperation($path, $patchEntryInput->from)];
        }
        else if ($op === PatchOperation::COPY) {
            if (property_exists($patchEntryInput, 'from') !== true) {
                return ["Copy operation must contain an entry for 'from'", null];
            }
            return [null, new CopyPatchOperation($path, $patchEntryInput->from)];
        }
        else {
            return ["Unknown operation '$op'", null];
        }
    }

    private static function createPatchEntryForArray($op, $path, $patchEntryInput)
    {
//        if (Functions::array_value_exists($this->allowedOps, $op) !== true) {
//            $message = sprintf(
//                "Op '%s' is not supported for this endpoint.",
//                $op
//            );
//            return [$message, null];
//        }

        if ($op === PatchOperation::TEST) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Test operation must contain an entry for 'value'", null];
            }
            return [null, new TestPatchOperation($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchOperation::REMOVE) {
            return [null, new RemovePatchOperation($path)];
        }
        else if ($op === PatchOperation::ADD) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Add operation must contain an entry for 'value'", null];
            }
            return [null, new AddPatchOperation($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchOperation::REPLACE) {
            if (array_key_exists('value', $patchEntryInput) !== true) {
                return ["Replace operation must contain an entry for 'value'", null];
            }
            return [null, new ReplacePatchOperation($path, $patchEntryInput['value'])];
        }
        else if ($op === PatchOperation::MOVE) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Move operation must contain an entry for 'from'", null];
            }
            return [null, new MovePatchOperation($path, $patchEntryInput['from'])];
        }
        else if ($op === PatchOperation::COPY) {
            if (array_key_exists('from', $patchEntryInput) !== true) {
                return ["Copy operation must contain an entry for 'from'", null];
            }
            return [null, new CopyPatchOperation($path, $patchEntryInput['from'])];
        }
        else {
            return ["Unknown operation '$op'", null];
        }
    }


    private static function checkObjectEntryForValidity($patchEntryInput)
    {
        if (property_exists($patchEntryInput, 'op') === false) {
            return ["missing 'op'", null];
        }
        if (property_exists($patchEntryInput, 'path') === false) {
            return ["missing 'path'", null];
        }

        return self::createPatchEntryForObject(
            $patchEntryInput->op,
            $patchEntryInput->path,
            $patchEntryInput
        );
    }

    private static function checkArrayEntryForValidity($patchEntry)
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

//    public function __invoke(string $name, $_): ValidationResult
    public static function convertInputToPatchObjects($value)
    {
//        // TODO - could check $_ is not null here, to prevent Patch being
//        // used as anything other than first in list.
//        $value = $this->input->get();
//        if (is_array($value) !== true) {
//            $message = sprintf(
//                "Patch '%s' must be an array of values, each with op, path and value set",
//                $name
//            );
//
//            return ValidationResult::errorResult($message);
//        }

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
            else if (is_object($patchEntryInput) === true) {
                [$error, $patchEntry] = self::checkObjectEntryForValidity($patchEntryInput);
            }
            else {
                $error = "Patch entry $count is neither an object or an array.";
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

            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult($patchEntries);
    }

//    public function updateParamDescription(ParamDescription $paramDescription)
//    {
//        // TODO: Implement updateParamDescription() method.
//    }
}
