<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetString;

use Params\Param;
use Params\ProcessRule\MaxLength;
use Params\SafeAccess;
use Params\Create\CreateFromVarMap;
use Params\Create\CreateOrErrorFromVarMap;
use Params\ExtractRule\GetArrayOfType;
use Params\InputParameterList;

class ItemListParams implements InputParameterList
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    /** @var \ParamsTest\Integration\ReviewScore[]  */
    private $items;

    /** @var string */
    private $description;

    /**
     * @param \ParamsTest\Integration\ReviewScore[] $items
     * @param string $description
     */
    public function __construct(array $items, string $description)
    {
        $this->items = $items;
        $this->description = $description;
    }

    public static function getInputParameterList()
    {
        return [
            new Param(
                'items',
                new GetArrayOfType(ReviewScore::class)
            ),
            new Param(
                'description',
                new GetString(),
                new MaxLength(120)
            ),
        ];
    }

    /**
     * @return ReviewScore[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
