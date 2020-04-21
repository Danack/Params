<?php

declare(strict_types = 1);

namespace ParamsTest\Integration;

use Params\Messages;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;

/**
 * @coversNothing
 */
class PasswordDoubleCheckTest extends BaseTestCase
{
    /**
     * @covers \ParamsTest\Integration\PasswordDoubleCheck
     */
    public function testWorks()
    {
        $password = 'abcde12345';
        $data = [
            'days' => 5,
            'password' => $password,
            'password_repeat' => $password,
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $error] = PasswordDoubleCheck::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertEmpty($error);

        $this->assertInstanceOf(PasswordDoubleCheck::class, $duplicateParams);
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
                    Messages::ERROR_NO_PREVIOUS_PARAM
                ],
            ]
        ];
    }

    /**
     * @covers \ParamsTest\Integration\PasswordDoubleCheck
     */
    public function testDifferentValue()
    {
        $data = [
            'password' => 'abcde12345',
            'password_repeat' => 'zyx12345'
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheck::createOrErrorFromVarMap(
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
     * @covers \ParamsTest\Integration\PasswordDoubleCheck
     */
    public function testMissingPreviousValue()
    {
        $data = [
            'password_repeat' => 'zyx12345'
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheck::createOrErrorFromVarMap(
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
            Messages::ERROR_NO_PREVIOUS_PARAM,
            $validationProblems
        );
    }


    /**
     * @covers \ParamsTest\Integration\PasswordDoubleCheck
     * @group needs_fixing
     */
    public function testWrongTypePreviousValue()
    {
        $data = [
            'password' => 'zyx12345',
            'password_repeat' => 5
        ];

        /** @var PasswordDoubleCheck $duplicateParams */
        [$duplicateParams, $validationProblems] = PasswordDoubleCheck::createOrErrorFromVarMap(
            new ArrayVarMap($data)
        );

        $this->assertNull($duplicateParams);
        $this->assertCount(1, $validationProblems);

        $this->assertValidationProblemRegexp(
            '/password_repeat',
            Messages::ERROR_DIFFERENT_TYPES,
            $validationProblems
        );
    }
}
