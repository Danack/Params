<?php

declare(strict_types = 1);

namespace ParamsTest\Create;

use ParamsTest\BaseTestCase;
use ParamsTest\Integration\IntArrayParams;
use ParamsTest\MockRequest;
use function JsonSafe\json_encode_safe;

/**
 * @coversNothing
 */
class CreateOrErrorFromRequestTest extends BaseTestCase
{
    /**
     * @covers \Params\Create\CreateOrErrorFromRequest
     */
    public function testWorks()
    {
        $name = 'John';
        $values = [3, 6, 9, 12];
        $data = [
            'name' => $name,
            'counts' => $values
        ];

        $request = MockRequest::createfromQueryParams($data);

        [$intArrayParams, $errors] =  IntArrayParams::createOrErrorFromRequest($request);

        $this->assertEmpty($errors);
        $this->assertInstanceOf(IntArrayParams::class, $intArrayParams);
        $this->assertSame($name, $intArrayParams->getName());
        $this->assertSame($values, $intArrayParams->getCounts());
    }
}
