<?php

declare(strict_types = 1);

namespace ParamsTest\DTOTypes;

use ParamsTest\PropertyTypes\MultipleBasicArray;
use Params\InputParameterListFromAttributes;

class DTOThatHasArrayOfParam
{
    use InputParameterListFromAttributes;

    public function __construct(
        #[MultipleBasicArray('quantities')]
        public array $quantities,
        //        #[Quantity('total')]
        //        public float $total,
    ) {
    }
}
