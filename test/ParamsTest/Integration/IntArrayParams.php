<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\Create\CreateFromArray;
use Params\Create\CreateOrErrorFromArray;
use Params\FirstRule\GetArrayOfInt;
use Params\SubsequentRule\MaxIntValue;
use Params\SubsequentRule\MinIntValue;
use Params\SafeAccess;
use Params\SubsequentRule\MaxLength;
use Params\FirstRule\GetString;

class IntArrayParams
{
    use SafeAccess;
    use CreateFromArray;
    use CreateOrErrorFromArray;

    /** @var string  */
    private $name;

    /** @var int[] */
    private $counts;

    /**
     *
     * @param string $name
     * @param int[] $values
     */
    public function __construct(string $name, array $counts)
    {
        $this->name = $name;
        $this->counts = $counts;
    }


    public static function getRules()
    {
        return [
            'name' => [
                new GetString(),
                new MaxLength(16),
            ],
            'counts' => [
               new GetArrayOfInt(
                   new MinIntValue(1),
                   new MaxIntValue(50)
               ),
               new ArrayAllMultiplesOf(3)
            ]
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
     * @return int[]
     */
    public function getCounts(): array
    {
        return $this->counts;
    }
}
