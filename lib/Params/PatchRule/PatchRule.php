<?php

declare(strict_types = 1);

namespace Params\PatchRule;

use Params\PatchOperation\PatchOperation;

/**
 * An interface for objects that contain info on how to convert
 * PatchOperations to ValueObjects.
 */
interface PatchRule
{
    public function getPathRegex(): string;

    /**
     * @return class-string<mixed>
     */
    public function getClassName(): string;

    public function getOpType(): string;

    /**
     * @return \Params\Param[]
     */
    public function getRules(): array;
}
