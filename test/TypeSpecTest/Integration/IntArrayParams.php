<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\Create\CreateFromArray;
use TypeSpec\Create\CreateFromJson;
use TypeSpec\Create\CreateFromRequest;
use TypeSpec\Create\CreateOrErrorFromArray;
use TypeSpec\Create\CreateOrErrorFromJson;
use TypeSpec\Create\CreateOrErrorFromRequest;
use TypeSpec\ExtractRule\GetArrayOfInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\SafeAccess;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\TypeSpec;

class IntArrayParams implements TypeSpec
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

    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'name',
                new GetString(),
                new MinLength(4),
                new MaxLength(16)
            ),
            new InputTypeSpec(
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
