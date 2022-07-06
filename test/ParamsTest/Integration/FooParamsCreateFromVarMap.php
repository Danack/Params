<?php

declare(strict_types=1);

namespace ParamsTest\Integration;

use Type\Create\CreateFromVarMap;

class FooParamsCreateFromVarMap extends FooParams
{
    use CreateFromVarMap;
}
