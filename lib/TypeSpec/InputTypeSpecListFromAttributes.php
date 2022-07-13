<?php

declare(strict_types = 1);

namespace TypeSpec;

trait InputTypeSpecListFromAttributes
{
    /**
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array
    {
        return getInputTypeSpecListFromAnnotations(get_called_class());
    }
}
