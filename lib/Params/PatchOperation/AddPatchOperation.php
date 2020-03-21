<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class AddPatchOperation implements PatchOperation
{
    // Example - { "op": "add", "path": "/a/b/c", "value": [ "foo", "bar" ] }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * AddPatchEntry constructor.
     * @param string $path
     * @param mixed $value
     */
    public function __construct(string $path, $value /*, this needs a type */)
    {
        $this->path = $path;
        $this->value = $value;
    }

    public function getOpType(): string
    {
        return "add";
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFrom(): string
    {
        throw new LogicException("Calling 'getFrom' on a AddPatchEntry is meaningless.");
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
