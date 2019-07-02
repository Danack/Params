<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use ParamsTest\BaseTestCase;


class IntArrayParamsTest extends BaseTestCase
{
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $intArrayParams = IntArrayParams::createFromArray($data);

        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }

    public function testBadInt()
    {
        $name = 'John';
        $values = [1, 2, "3 bananas", 4];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        [$intArrayParams, $errors] = IntArrayParams::createOrErrorFromArray($data);

        $this->assertNull($intArrayParams);


        $this->assertCount(1, $errors);
        $this->assertSame(
            "Value for 'counts[2]' must contain only digits.",
            $errors[0]
        );
    }
}
