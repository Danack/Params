<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpecTest\PropertyTypes\MultipleBasicDTO;
use TypeSpecTest\PropertyTypes\Quantity;
use TypeSpec\InputTypeSpecListFromAttributes;

class AdvancedDTO
{
    use InputTypeSpecListFromAttributes;

    public function __construct(
        #[MultipleBasicDTO('colors')]
        public array $colors,
        #[Quantity('total')]
        public float $total,
    ) {
    }
}
