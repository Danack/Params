<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\FirstRule\GetString;

use Params\SubsequentRule\MaxLength;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\Create\CreateOrErrorFromVarMap;
use Params\FirstRule\GetArrayOfType;

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

    public static function getRules()
    {
        return [
            'items' => [
                new GetArrayOfType(ItemParams::class),
            ],
            'description' => [
                new GetString(),
                new MaxLength(120)
            ],
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
