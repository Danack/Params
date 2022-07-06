<?php

declare(strict_types = 1);


namespace Type;

/**
 *
 */
interface Type
{
    /**
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyDefinitionList(): array;
}
