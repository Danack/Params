<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\Create\CreateOrErrorFromArray;
use Params\ExtractRule\GetString;
use Params\InputToParamInfoListAware;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\SafeAccess;
use Params\Param;
use ParamsExample\MacAddressType;

class ComputerDetailsParams implements InputToParamInfoListAware
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
     * @return \Params\Param[]
     */
    public static function getInputToParamInfoList()
    {
        return [
            new Param(
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
