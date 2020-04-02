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

    /** @var \ParamsTest\Integration\ItemParams[]  */
    private $items;

    /** @var string */
    private $description;

    /**
     * @param \ParamsTest\Integration\ItemParams[] $items
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
                new GetArrayOfType(ItemParams::class)
            ),
            new Param(
                'description',
                new GetString(),
                new MaxLength(120)
            ),
        ];
    }

    /**
     * @return ItemParams[]
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
