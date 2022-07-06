<?php

declare(strict_types=1);


namespace Type;

interface TypeProperty
{
    public function getPropertyRules(): PropertyDefinition;
}
