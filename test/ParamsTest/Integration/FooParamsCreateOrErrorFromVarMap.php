<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\Create\CreateOrErrorFromVarMap;

class FooParamsCreateOrErrorFromVarMap extends FooParams
{
    use CreateOrErrorFromVarMap;
}
