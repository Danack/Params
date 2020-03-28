<?php

declare(strict_types = 1);

namespace Params\PatchRule;

use Params\PatchOperation\PatchOperation;

class PatchAdd implements PatchRule
{
    private string $pathRegex;

    /** @var class-string */
    private string $className;

    /**
     * @var \Params\Param[]
     */
    private array $rules;

    /**
     *
     * @param string $pathRegex
     * @param class-string $className
     * @param \Params\Param[] $rules
     */
    public function __construct(string $pathRegex, string $className, array $rules)
    {
        $this->pathRegex = $pathRegex;
        $this->className = $className;
        $this->rules     = $rules;
    }

    /**
     * @return string
     */
    public function getPathRegex(): string
    {
        return $this->pathRegex;
    }

    /**
     * @return class-string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getOpType(): string
    {
        return PatchOperation::ADD;
    }

    /**
     * @return \Params\Param[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
