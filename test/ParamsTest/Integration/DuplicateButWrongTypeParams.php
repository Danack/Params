<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\SafeAccess;
use Type\ExtractRule\GetString;
use Type\Create\CreateOrErrorFromVarMap;
use Type\ProcessRule\DuplicatesParam;

class DuplicateButWrongTypeParams
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    private int $days;

    private string $days_repeat;

    public function __construct(int $days, string $days_repeat)
    {
        $this->days = $days;
        $this->days_repeat = $days_repeat;
    }

    public static function getInputParameterList()
    {
        return [
            new PropertyDefinition(
                'days',
                new GetInt()
            ),
            new PropertyDefinition(
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
