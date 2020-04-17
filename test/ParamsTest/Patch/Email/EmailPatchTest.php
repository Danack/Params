<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use function Params\createOperationsFromPatch;
use ParamsTest\Patch\Email\CheckUserEmailMatches;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class EmailPatchTest extends BaseTestCase
{
    /**
     * @group debug
     */
    public function testVeryBasic()
    {
        $emailOld = "john@example.com";
        $emailNew = "jane@example.com";

        $data = [[
            "op" => "test",
            "path" => "/email/user",
            "value" => $emailOld
        ],
        [
        "op" => "replace",
            "path" => "/email/user",
            "value" => $emailNew
        ]
        ];

        $operations = createOperationsFromPatch(
            EmailPatchParams::getInputParameterList(),
            json_decode_safe(json_encode($data))
        );

        $this->assertCount(1, $operations);

        /** @var CheckUserEmailMatches $userEmailMatches */
        $userEmailMatches = $operations[0];

        $this->assertInstanceOf(CheckUserEmailMatches::class, $userEmailMatches);
        $this->assertSame($emailOld, $userEmailMatches->getEmail());
    }
}
