<?php

declare(strict_types = 1);

namespace ParamsExample\DTOTypes;

use ParamsExample\PropertyTypes\KnownColors;
use ParamsExample\PropertyTypes\Quantity;
use Params\InputParameterListFromAttributes;

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