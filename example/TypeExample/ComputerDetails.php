<?php

declare(strict_types=1);

namespace TypeExample;

use Type\Create\CreateOrErrorFromArray;
use Type\ExtractRule\GetString;
use Type\Type;
use Type\ProcessRule\MinLength;
use Type\ProcessRule\MaxLength;
use Type\SafeAccess;
use Type\PropertyDefinition;
use TypeExample\MacAddressType;

class ComputerDetails implements Type
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
     * @return \Type\PropertyDefinition[]
     */
    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
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
