<?php

declare(strict_types = 1);

namespace TypeExample;

use Type\ExtractRule\GetString;
use Type\PropertyDefinition;

class MacAddressType
{
    /** @var string */
    private $value;

    /**
     *
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public static function getParamInfo(string $inputName): PropertyDefinition
    {
        return new PropertyDefinition(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
