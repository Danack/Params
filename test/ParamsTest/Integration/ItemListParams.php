<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\ExtractRule\GetString;

use Type\PropertyDefinition;
use Type\ProcessRule\MaxLength;
use Type\SafeAccess;
use Type\Create\CreateFromVarMap;
use Type\Create\CreateOrErrorFromVarMap;
use Type\ExtractRule\GetArrayOfType;
use Type\Type;

class ItemListParams implements Type
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

    public static function getPropertyDefinitionList(): array
    {
        return [
            new PropertyDefinition(
                'items',
                new GetArrayOfType(ReviewScore::class)
            ),
            new PropertyDefinition(
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
