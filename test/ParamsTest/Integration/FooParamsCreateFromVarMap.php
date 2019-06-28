<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Params\Create\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
