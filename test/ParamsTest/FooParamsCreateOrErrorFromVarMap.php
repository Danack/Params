<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\Create\CreateOrErrorFromVarMap;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
