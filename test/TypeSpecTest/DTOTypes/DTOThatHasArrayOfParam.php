<?php

declare(strict_types = 1);

namespace TypeSpecTest\DTOTypes;

use TypeSpecTest\PropertyTypes\MultipleBasicArray;
use TypeSpec\InputTypeSpecListFromAttributes;

class DTOThatHasArrayOfParam
{
    use InputTypeSpecListFromAttributes;

    public function __construct(
        #[MultipleBasicArray('quantities')]
        public array $quantities,
        //        #[Quantity('total')]
        //        public float $total,
    ) {
    }
}
