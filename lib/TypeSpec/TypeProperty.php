<?php

declare(strict_types=1);


namespace TypeSpec;

interface TypeProperty
{
    public function getPropertyRules(): InputTypeSpec;
}
