<?php

declare(strict_types = 1);

namespace ParamsExample\DTOTypes;

use ParamsExample\PropertyTypes\KnownColors;

use Params\Create\CreateFromVarMap;
use Params\InputParameterListFromAttributes;
use ParamsExample\PropertyTypes\Quantity;


class TestDTO
{
    use CreateFromVarMap;
    use InputParameterListFromAttributes;

    public function __construct(
        #[KnownColors('color')]
        public string $color,
        #[Quantity('quantity')]
        public float $quantity,
    ) {
    }
}