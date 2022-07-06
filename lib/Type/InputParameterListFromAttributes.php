<?php

declare(strict_types = 1);

namespace Type;

trait InputParameterListFromAttributes
{
    /**
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyRulesList(): array
    {
        return getPropertyDefinitionsFromAnnotations(get_called_class());
    }
}
