<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\Create\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
