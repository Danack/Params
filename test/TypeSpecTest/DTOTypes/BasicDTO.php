<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpecTest\PropertyTypes\KnownColors;
use TypeSpecTest\PropertyTypes\Quantity;
use TypeSpec\InputTypeSpecListFromAttributes;

class BasicDTO
{
    use InputTypeSpecListFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public float $quantity,
    ) {
    }
}
