<?php

declare(strict_types = 1);

namespace ParamsTest\DTOTypes;

use ParamsTest\PropertyTypes\KnownColors;
use ParamsTest\PropertyTypes\Quantity;
use Type\InputParameterListFromAttributes;

class BasicDTO
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
