<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Create\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
