<?php

declare(strict_types=1);

namespace Params;

use Params\PatchOperation\PatchOperation;

/**
 *
 */
class PatchValidationResult
{
    /**
     * The patch operatations that were extracted from the source data.
     * This list may be incomplete if the data contained errors
     * e.g. missing or unknown op type.
     *
     * @var \Params\PatchOperation\PatchOperation[]
     */
    private array $patchOperations;


    /** @var \Params\PatchObjectProblem[] */
    private array $patchObjectProblems;

    private bool $isFinalResult;

    /**
     * @return \Params\PatchOperation\PatchOperation[]
     */
    public function getPatchOperations()
    {
        return $this->patchOperations;
    }

    /**
     * ValidationResult constructor.
     * @param \Params\PatchOperation\PatchOperation[] $patchOperations
     * @param \Params\PatchObjectProblem[] $problemMessages
     * @param bool $isFinalResult
     */
    private function __construct(
        array $patchOperations,
        array $problemMessages,
        bool $isFinalResult
    ) {
        $this->patchOperations = $patchOperations;
        $this->patchObjectProblems = $problemMessages;
        $this->isFinalResult = $isFinalResult;
    }
    /**
     * @param PatchObjectProblem[] $validationProblems
     * @param PatchOperation[] $patchOperations
     * @return self
     */
    public static function thisIsMultipleErrorResult(
        array $validationProblems,
        array $patchOperations
    ): self {
        foreach ($validationProblems as $key => $validationProblem) {
            if (is_int($key)  === false) {
                throw new \LogicException("Key for array must be integer");
            }
            /**
             * This is technically unneeded.
             * @psalm-suppress DocblockTypeContradiction
             */
            if (!($validationProblem instanceof PatchObjectProblem)) {
                $message = sprintf(
                    "Array must contain only 'PatchValidationProblem's instead got [%s]",
                    getTypeForErrorMessage($validationProblem)
                );

                throw new \LogicException(
                    $message
                );
            }
        }

        return new self($patchOperations, $validationProblems, true);
    }

    /**
     * @param PatchOperation[] $patchOperations
     * @return PatchValidationResult
     */
    public static function valueResult(array $patchOperations)
    {
        return new self($patchOperations, [], false);
    }


    /**
     * @return \Params\PatchObjectProblem[]
     */
    public function getPatchObjectProblems(): array
    {
        return $this->patchObjectProblems;
    }

    /**
     * Whether any errors were found.
     */
    public function anyErrorsFound(): bool
    {
        if (count($this->patchObjectProblems) !== 0) {
            return true;
        }
        return false;
    }

    /**
     * Return true if there should not be any more processing of the
     * rules for this parameter. e.g. both errors and null results stop
     * the processing.
     *
     * @return bool
     */
    public function isFinalResult(): bool
    {
        return $this->isFinalResult;
    }
}
