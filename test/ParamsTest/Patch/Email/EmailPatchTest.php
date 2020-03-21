<?php

declare(strict_types = 1);

namespace ParamsTest\Patch\Email;

use Params\Params;
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
        $email = "john@example.com";

        $data = [[
            "op" => "test",
            "path" => "/email/user",
            "value" => $email
        ]];

        $this->markTestSkipped("Patch param values can be values, not VarMaps so need different way of processing 'FirstRules'");

        return;

        $operations = Params::createPatch(
            EmailPatchParams::getInputToParamInfoList(),
            json_decode_safe(json_encode($data))
        );

        $this->assertCount(1, $operations);

        /** @var CheckUserEmailMatches $userEmailMatches */
        $userEmailMatches = $operations[0];

        $this->assertInstanceOf(CheckUserEmailMatches::class, $userEmailMatches);
        $this->assertSame($email, $userEmailMatches->getEmail());
    }
}
