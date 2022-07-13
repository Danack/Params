<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\SafeAccess;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\Create\CreateOrErrorFromVarMap;
use TypeSpec\ProcessRule\DuplicatesParam;

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
            new InputTypeSpec(
                'days',
                new GetInt()
            ),
            new InputTypeSpec(
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
