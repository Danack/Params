<?php

declare(strict_types = 1);

namespace TypeExample\DTOTypes;

use TypeExample\PropertyTypes\KnownColors;
use TypeExample\PropertyTypes\Quantity;
use Type\InputParameterListFromAttributes;

class TestDTO
{
    use InputParameterListFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public float $quantity,
    ) {
    }
}