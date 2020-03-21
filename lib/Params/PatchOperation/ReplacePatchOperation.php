<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class ReplacePatchOperation implements PatchOperation
{
    // Example - { "op": "replace", "path": "/a/b/c", "value": 42 }

    /** @var string */
    private $path;

    /** @var mixed */
    private $value;

    /**
     * ReplacePatchEntry constructor.
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
        return 'replace';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFrom(): string
    {
        throw new LogicException("Calling 'getFrom' on a ReplacePatchEntry is meaningless.");
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
