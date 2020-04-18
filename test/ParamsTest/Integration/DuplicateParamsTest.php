<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\Messages;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\Messages as DuplicatesParamRule;

/**
 * @group v2
 * @coversNothing
 */
class DuplicateParamsTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testWorks()
    {
        $password = 'abcde12345';
        $data = [
            'days' => 5,
            'password' => $password,
            'password_repeat' => $password,
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $error] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertEmpty($error);

        $this->assertInstanceOf(DuplicateParams::class, $duplicateParams);
        $this->assertSame($password, $duplicateParams->getPassword());
        $this->assertSame($password, $duplicateParams->getPasswordRepeat());
    }

    public function providesErrors()
    {
        return [
            [

                [
                    'days' => 6,
                    'password_repeat' => 'zyx12345',
                    Messages::ERROR_DIFFERENT_TYPES
                ],

                [
                    'password_repeat' => 'zyx12345',
                    DuplicatesParamRule::ERROR_NO_PREVIOUS_PARAM
                ],
            ]
        ];
    }

    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testDifferentValue()
    {
        $data = [
            'password' => 'abcde12345',
            'password_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $validationProblems] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblem(
            '/password_repeat',
            "Parameter is different to parameter 'password'.",
            $validationProblems
        );
    }

    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testMissingPreviousValue()
    {
        $data = [
            'password_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $validationProblems] = DuplicateParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(2, $validationProblems);

        $this->assertValidationProblem(
            '/password',
            Messages::VALUE_NOT_SET,
            $validationProblems
        );

        $this->assertValidationProblemRegexp(
            '/password_repeat',
            DuplicatesParamRule::ERROR_NO_PREVIOUS_PARAM,
            $validationProblems
        );
    }


    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testWrongTypePreviousValue()
    {
        $data = [
            'days' => 5,
            'days_repeat' => 'zyx12345'
        ];

        /** @var DuplicateParams $duplicateParams */
        [$duplicateParams, $validationProblems] = DuplicateButWrongTypeParams::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblemRegexp(
            '/days_repeat',
            Messages::ERROR_DIFFERENT_TYPES,
            $validationProblems
        );
    }
}
