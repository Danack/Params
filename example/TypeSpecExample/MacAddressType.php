<?php

declare(strict_types = 1);

namespace TypeSpecExample;

use TypeSpec\ExtractRule\GetString;
use TypeSpec\InputTypeSpec;

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

    public static function getParamInfo(string $inputName): InputTypeSpec
    {
        return new InputTypeSpec(
            $inputName,
            new GetString(),
            new RespectMacRule()
        );
    }
}
