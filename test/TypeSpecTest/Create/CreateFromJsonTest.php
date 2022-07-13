<?php

declare(strict_types = 1);

namespace TypeSpecTest\Create;

use TypeSpecTest\BaseTestCase;
use TypeSpecTest\Integration\IntArrayParams;
use function JsonSafe\json_encode_safe;

/**
 * @coversNothing
 */
class CreateFromJsonTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Create\CreateFromJson
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $json = json_encode_safe($data);

        $intArrayParams = IntArrayParams::createFromJson($json);

        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }
}
