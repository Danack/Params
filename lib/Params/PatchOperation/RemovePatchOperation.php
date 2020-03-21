<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class RemovePatchOperation implements PatchOperation
{

    // Example - { "op": "remove", "path": "/a/b/c" }

    /** @var string */
    private $path;

    /**
     * RemovePatchEntry constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getOpType(): string
    {
        return "remove";
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFrom(): string
    {
        throw new LogicException("Calling 'getFrom' on a TestPatchEntry is meaningless.");
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        throw new LogicException("Calling 'getValue' on a RemovePatchEntry is meaningless.");
    }
}
