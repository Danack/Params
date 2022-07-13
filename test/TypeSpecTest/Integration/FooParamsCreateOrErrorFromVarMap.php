<?php

declare(strict_types=1);

namespace TypeSpecTest\Integration;

use TypeSpec\Create\CreateOrErrorFromVarMap;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
