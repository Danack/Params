<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetInt;
use Params\InputToParamInfo;
use Params\SafeAccess;
use Params\ExtractRule\GetString;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\Create\CreateOrErrorFromVarMap;
use Params\ProcessRule\DuplicatesParam;

class DuplicateButWrongTypeParams
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    /** @var int */
    private $days;

    /** @var string */
    private $days_repeat;

    public function __construct(int $days, string $days_repeat)
    {
        $this->days = $days;
        $this->days_repeat = $days_repeat;
    }

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'days',
                new GetInt()
            ),
            new InputToParamInfo(
                'days_repeat',
                new GetString(),
                new DuplicatesParam('days')
            ),
        ];
    }

    /**
     * @return int
     */
    public function getDays(): int
    {
        return $this->days;
    }

    /**
     * @return string
     */
    public function getDaysRepeat(): string
    {
        return $this->days_repeat;
    }
}
