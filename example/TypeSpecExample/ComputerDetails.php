<?php

declare(strict_types=1);

namespace TypeSpecExample;

use TypeSpec\Create\CreateOrErrorFromArray;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\TypeSpec;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\SafeAccess;
use TypeSpec\InputTypeSpec;
use TypeSpecExample\MacAddressType;

class ComputerDetails implements TypeSpec
{
    use SafeAccess;
    use CreateOrErrorFromArray;

    /** @var string */
    private $name;

    /** @var string */
    private $macAddress;

    /**
     *
     * @param string $name
     * @param string $macAddress
     */
    public function __construct(string $name, string $macAddress)
    {
        $this->name = $name;
        $this->macAddress = $macAddress;
    }

    /**
     * @return \TypeSpec\InputTypeSpec[]
     */
    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'name',
                new GetString(),
                new MinLength(2),
                new MaxLength(1024)
            ),

            MacAddressType::getParamInfo('macAddress'),
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMacAddress(): string
    {
        return $this->macAddress;
    }
}
