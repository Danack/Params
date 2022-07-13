<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\ExtractRule\GetString;

use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\SafeAccess;
use TypeSpec\Create\CreateFromVarMap;
use TypeSpec\Create\CreateOrErrorFromVarMap;
use TypeSpec\ExtractRule\GetArrayOfType;
use TypeSpec\TypeSpec;

class ItemListParams implements TypeSpec
{
    use SafeAccess;
    use CreateFromVarMap;
    use CreateOrErrorFromVarMap;

    /** @var \TypeSpecTest\Integration\ReviewScore[]  */
    private $items;

    /** @var string */
    private $description;

    /**
     * @param \TypeSpecTest\Integration\ReviewScore[] $items
     * @param string $description
     */
    public function __construct(array $items, string $description)
    {
        $this->items = $items;
        $this->description = $description;
    }

    public static function getInputTypeSpecList(): array
    {
        return [
            new InputTypeSpec(
                'items',
                new GetArrayOfType(ReviewScore::class)
            ),
            new InputTypeSpec(
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
