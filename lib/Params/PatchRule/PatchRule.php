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

    public function getClassName(): string;

    public function getOpType(): string;

    public function getRules(): array;
}
