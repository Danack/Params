<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\ExtractRule\GetString;

use Params\InputToParamInfo;
use Params\ProcessRule\MaxLength;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\Create\CreateOrErrorFromVarMap;
use Params\ExtractRule\GetArrayOfType;

class ItemListParams
{
    use SafeAccess;
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

    public static function getInputToParamInfoList()
    {
        return [
            new InputToParamInfo(
                'items',
                new GetArrayOfType(ItemParams::class)
            ),
            new InputToParamInfo(
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
