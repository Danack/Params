<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\Create\CreateOrErrorFromArray;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MaxLength;
use Params\SafeAccess;
use VarMap\VarMap;

class ComputerDetailsParams
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

    public static function getRules()
    {
        return [
            'name' => [
                new GetString(),
                new MinLength(2),
                new MaxLength(1024)
            ],
            'mac_address' => [
                new GetString(),
                new RespectMacRule(),
            ],
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
