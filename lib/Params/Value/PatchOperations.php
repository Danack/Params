<?php

declare(strict_types=1);

namespace Params\Value;

use Params\PatchOperation\PatchOperation;

class PatchOperations
{
    /** @var PatchOperation[] */
    private $patchOperations;

    /**
     * Patch constructor.
     */
    public function __construct(PatchOperation ...$patchOperations)
    {
        $this->patchOperations = $patchOperations;
    }

    /**
     * @return PatchOperation[]
     */
    public function getPatchOperations(): array
    {
        return $this->patchOperations;
    }
}
