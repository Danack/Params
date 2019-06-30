<?php

declare(strict_types=1);

namespace Params\PatchOperation;

use Params\Exception\LogicException;
use Params\PatchOperation\PatchOperation;

class MovePatchOperation implements PatchOperation
{
    // Example - { "op": "move", "from": "/a/b/c", "path": "/a/b/d" }

    /** @var string */
    private $path;

    /** @var string|null */
    private $from;

    /**
     * MovePatchEntry constructor.
     * @param string $path
     * @param null|string $from
     */
    public function __construct(string $path, ?string $from)
    {
        $this->path = $path;
        $this->from = $from;
    }

    public function getOpType()
    {
        return self::MOVE;
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
        throw new LogicException("Calling 'getValue' on a MovePatchEntry is meaningless.");
    }
}
