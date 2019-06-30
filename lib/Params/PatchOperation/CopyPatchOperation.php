<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class CopyPatchOperation implements PatchOperation
{
    // Example - { "op": "copy", "from": "/a/b/c", "path": "/a/b/e" }

    /** @var string */
    private $path;

    /** @var string */
    private $from;

    /**
     * CopyPatchEntry constructor.
     * @param string $path
     * @param string $from
     */
    public function __construct(string $path, string $from)
    {
        $this->path = $path;
        $this->from = $from;
    }

    public function getOpType()
    {
        return "copy";
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getValue()
    {
        throw new LogicException("Calling 'getValue' on a CopyPatchEntry is meaningless.");
    }
}
