<?php

declare(strict_types = 1);

namespace ParamsTest\DTOTypes;

use ParamsTest\PropertyTypes\MultipleBasicDTO;
use ParamsTest\PropertyTypes\Quantity;
use Params\InputParameterListFromAttributes;

class AdvancedDTO
{
    use InputParameterListFromAttributes;

    public function __construct(
        #[MultipleBasicDTO('colors')]
        public array $colors,
        #[Quantity('total')]
        public float $total,
    ) {
    }
}
