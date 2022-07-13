<?php

declare(strict_types = 1);


namespace TypeSpec;

/**
 *
 */
interface TypeSpec
{
    /**
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array;
}
