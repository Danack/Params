<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\FirstRule\GetInt;
use Params\SafeAccess;
use Params\FirstRule\GetString;
use Params\SubsequentRule\MinLength;
use Params\SubsequentRule\MaxLength;
use Params\Create\CreateOrErrorFromVarMap;
use Params\SubsequentRule\DuplicatesParam;

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

    public static function getRules()
    {
        return [
            'days' => [
                new GetInt()
            ],
            'days_repeat' => [
                new GetString(),
                new DuplicatesParam('days'),
            ],

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
