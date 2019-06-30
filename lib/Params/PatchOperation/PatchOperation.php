<?php

declare(strict_types=1);

namespace Params\PatchOperation;

/**
 * Common interface for objects that represent the raw unparsed
 * Patch operations.
 * @package Params\Value
 */
interface PatchOperation
{
    const TEST      = "test";
    const REMOVE    = "remove";
    const ADD       = "add";
    const REPLACE   = "replace";
    const MOVE      = "move";
    const COPY      = "copy";

    const ALL_OPS = [
        self::TEST,
        self::REMOVE,
        self::ADD,
        self::REPLACE,
        self::MOVE,
        self::COPY
    ];

    public function getOpType();

    public function getPath();

    public function getFrom();

    /**
     * @return mixed
     */
    public function getValue();
}
