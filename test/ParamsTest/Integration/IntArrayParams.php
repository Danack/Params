<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\Create\CreateFromArray;
use Type\Create\CreateFromJson;
use Type\Create\CreateFromRequest;
use Type\Create\CreateOrErrorFromArray;
use Type\Create\CreateOrErrorFromJson;
use Type\Create\CreateOrErrorFromRequest;
use Type\ExtractRule\GetArrayOfInt;
use Type\PropertyDefinition;
use Type\ProcessRule\MaxIntValue;
use Type\ProcessRule\MinIntValue;
use Type\SafeAccess;
use Type\ProcessRule\MinLength;
use Type\ProcessRule\MaxLength;
use Type\ExtractRule\GetString;
use Type\Type;

class IntArrayParams implements Type
{
    use SafeAccess;
    use CreateFromArray;
    use CreateFromJson;
    use CreateFromRequest;
    use CreateOrErrorFromArray;
    use CreateOrErrorFromJson;
    use CreateOrErrorFromRequest;


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

    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'name',
                new GetString(),
                new MinLength(4),
                new MaxLength(16)
            ),
            new PropertyDefinition(
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
