<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Rule\GetString;

use Params\Rule\MaxLength;
use Params\SafeAccess;
use VarMap\VarMap;
use Params\CreateOrErrorFromVarMap;
use Params\Rule\GetArrayOfType;

class ItemListParams
{
    use SafeAccess;
    use CreateOrErrorFromVarMap;

    /** @var \ParamsTest\ItemParams[]  */
    private $items;

    /** @var string */
    private $description;

    /**
     * @param \ParamsTest\ItemParams[] $items
     * @param string $description
     */
    public function __construct(array $items, string $description)
    {
        $this->items = $items;
        $this->description = $description;
    }

    public static function getRules(VarMap $variableMap)
    {
        return [
            'items' => [
                new GetArrayOfType($variableMap, ItemParams::class),
            ],
            'description' => [
                new GetString($variableMap),
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
