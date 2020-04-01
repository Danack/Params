<?php

declare(strict_types = 1);

namespace Params;

/**
 * Represents a problem with a JSON Patch object
 *
 * Examples would include a missing 'op' type, missing 'path' or other
 * invalid data that makes the PatchObject unusable
 *
 * i.e. gross data problems in the message format, rather than
 * 'business rules' applied to the processed data.
 */
class PatchObjectProblem
{
    /**
     * The count into the array of operations. For example operation
     * '1' below is invalid, as 'John' is not a valid Patch Object.
     *
     * [
     *   { "op": "test", "path": "/a/b/c", "value": "foo" },
     *   "John."
     *   { "op": "add", "path": "/a/b/c", "value": ["foo", "bar"] },
     * ]
     *
     *
     */
    private int $operationIndex;

    private string $problemMessage;

    /**
     *
     * @param int $operationIndex
     * @param string $description
     */
    public function __construct(int $operationIndex, string $description)
    {
        $this->operationIndex = $operationIndex;
        $this->problemMessage = $description;
    }

    /**
     * Returns the index of the operation that has the problem in the source data
     *
     * e.g. for this error
     *
     * [
     *   [ "op" => 'test', "path" => "/a/b/c", 'value' => 5], // ok
     *   [ "op" => 'add'], //missing path
     *   [ "op" => 'test', "path" => "/a/b/c", 'value' => 5], // ok
     * ]
     *
     * The index would be '1'.
     *
     * @return int
     */
    public function getOperationIndex(): int
    {
        return $this->operationIndex;
    }



    /**
     * @return string
     */
    public function getProblemMessage(): string
    {
        return $this->problemMessage;
    }

    /**
     * TODO - this probably shouldn't be here?
     * Need to think about where message formatting should be done.
     */
    public function toString(): string
    {
        return sprintf(
            "Operation %s: %s",
            $this->operationIndex,
            $this->problemMessage
        );
    }
}
