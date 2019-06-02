<?php

declare(strict_types=1);

namespace ParamsExample;

use Params\CreateOrErrorFromArray;
use Params\Rule\GetString;
use Params\Rule\MinLength;
use Params\Rule\MaxLength;
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

    /**
     * @param VarMap $variableMap
     * @return array
     */
    public static function getRules(VarMap $variableMap)
    {
        return [
            'name' => [
                new GetString($variableMap),
                new MinLength(2),
                new MaxLength(1024)
            ],
            'mac_address' => [
                new GetString($variableMap),
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
