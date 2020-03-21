<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class TestPatchOperation implements PatchOperation
{
    // Example - { "op": "test", "path": "/a/b/c", "value": "foo" }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * TestPatchEntry constructor.
     * @param string $path
     * @param mixed $value
     */
    public function __construct(string $path, $value)
    {
        $this->path = $path;
        $this->value = $value;
    }

    public function getOpType(): string
    {
        return "test";
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
        return $this->value;
    }
}
