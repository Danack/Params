<?php

declare(strict_types = 1);

namespace TypeSpecExample\DTOTypes;

use TypeSpecExample\PropertyTypes\KnownColors;
use TypeSpecExample\PropertyTypes\Quantity;
use TypeSpec\InputTypeSpecListFromAttributes;

class TestDTO
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