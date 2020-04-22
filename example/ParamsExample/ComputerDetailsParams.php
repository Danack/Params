<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\Create\CreateOrErrorFromArray;
use Params\ExtractRule\GetString;
use Params\InputParameterList;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\SafeAccess;
use Params\InputParameter;
use ParamsExample\MacAddressType;

class ComputerDetailsParams implements InputParameterList
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
     * @return \Params\InputParameter[]
     */
    public static function getInputParameterList(): array
    {
        return [
            new InputParameter(
                'name',
                new GetString(),
                new MinLength(2),
                new MaxLength(1024)
            ),

            MacAddressType::getParamInfo('mac_address'),
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
