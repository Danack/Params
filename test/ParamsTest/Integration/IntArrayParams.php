<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\Create\CreateFromArray;
use Params\Create\CreateFromJson;
use Params\Create\CreateOrErrorFromArray;
use Params\Create\CreateOrErrorFromJson;
use Params\ExtractRule\GetArrayOfInt;
use Params\InputParameter;
use Params\ProcessRule\MaxIntValue;
use Params\ProcessRule\MinIntValue;
use Params\SafeAccess;
use Params\ProcessRule\MinLength;
use Params\ProcessRule\MaxLength;
use Params\ExtractRule\GetString;

class IntArrayParams
{
    use SafeAccess;
    use CreateFromArray;
    use CreateFromJson;
    use CreateOrErrorFromArray;
    use CreateOrErrorFromJson;

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


    public static function getInputParameterList()
    {
        return [
            new InputParameter(
                'name',
                new GetString(),
                new MinLength(4),
                new MaxLength(16)
            ),
            new InputParameter(
                'counts',
                new GetArrayOfInt(
                    new MinIntValue(1),
                    new MaxIntValue(50)
                ),
                new ArrayAllMultiplesOf(3)
            )
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
