<?php

declare(strict_types=1);


namespace TypeSpec;

interface PropertyInputTypeSpec
{
    // this should return a PropertyInputTypeSpec
    public function getInputTypeSpec(): InputTypeSpec;
}
