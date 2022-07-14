<?php

declare(strict_types=1);

namespace TypeSpecTest\OpenApi;

use TypeSpec\InputTypeSpec;
use TypeSpec\OpenApi\ShouldNeverBeCalledParamDescription;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\ProcessRule\Enum;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ExtractRule\GetIntOrDefault;
use TypeSpec\ExtractRule\GetOptionalInt;
use TypeSpec\ExtractRule\GetOptionalString;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\ProcessRule\MaxLength;
use TypeSpec\ProcessRule\MinIntValue;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\ProcessRule\PositiveInt;
use TypeSpec\ProcessRule\Trim;
use TypeSpec\ProcessRule\ValidDate;
use TypeSpec\ProcessRule\ValidDatetime;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\AlwaysEndsRule;
use TypeSpec\Exception\OpenApiException;
use TypeSpec\ProcessRule\NullIfEmpty;

/**
 * @coversNothing
 */
class DescriptionTest extends BaseTestCase
{
    public function testEnum()
    {
        $values = [
            'available',
            'pending',
            'sold'
        ];
        $schemaExpectations = [
            'enum' => $values,
        ];

        $rules =  [
            new InputTypeSpec(
                'value',
                new GetString(),
                new Enum($values)
            ),
        ];
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testRequired()
    {
        $descriptionExpectations = [
            'required' => true,
        ];
        $rules = RequiredStringExample::getInputParameterList();
        $this->performFullTest([], $descriptionExpectations, $rules);
    }

    public function testMinLength()
    {
        $schemaExpectations = [
            'minLength' => RequiredStringExample::MIN_LENGTH,
        ];

        $rules = RequiredStringExample::getInputParameterList();
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaxLength()
    {
        $schemaExpectations = [
            'maxLength' => RequiredStringExample::MAX_LENGTH,
        ];

        $rules = RequiredStringExample::getInputParameterList();
        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testInt()
    {
        $descriptionExpectations = [
            'required' => true
        ];

        $schemaExpectations = [
            'type' => 'integer'
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetInt()
            ),
        ];

        $this->performFullTest($schemaExpectations, $descriptionExpectations, $rules);
    }

    public function testIntOrDefault()
    {
        $default = 5;
        $schemaExpectations = [
            'type' => 'integer',
            'default' => $default
        ];
        $paramExpectations = [
            'required' => false,
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetIntOrDefault($default)
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testStringOrDefault()
    {
        $default = 'foo';
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string',
            'default' => $default
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetStringOrDefault($default)
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalInt()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'integer'
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetOptionalInt()
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testOptionalString()
    {
        $paramExpectations = [
            'required' => false,
        ];
        $schemaExpectations = [
            'type' => 'string'
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetOptionalString()
            ),
        ];

        $this->performFullTest($schemaExpectations, $paramExpectations, $rules);
    }

    public function testMinInt()
    {
        $maxValue = 10;
        $schemaExpectations = [
            'minimum' => $maxValue,
            'exclusiveMinimum' => false
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetInt(),
                new MinIntValue($maxValue)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testMaximumLength()
    {
        $maxLength = 10;
        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesValidMinimumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @dataProvider providesValidMinimumLength
     */
    public function testMininumLength($minLength)
    {
        $schemaExpectations = [
            'minLength' => $minLength,
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new MinLength($minLength)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function providesInvalidMininumLength()
    {
        return [[0], [-1], [-2], [-3] ];
    }

    /**
     * @param $minLength
     * @dataProvider providesInvalidMininumLength
     */
    public function testInvalidMininumLength($minLength)
    {
        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new MinLength($minLength)
            ),
        ];

        $this->expectException(OpenApiException::class);
        OpenApiV300ParamDescription::createFromInputTypeSpecList($rules);
    }


    public function providesInvalidMaximumLength()
    {
        return [[0], [-1] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesInvalidMaximumLength
     */
    public function testInvalidMaximumLength($maxLength)
    {
        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $this->expectException(OpenApiException::class);
        OpenApiV300ParamDescription::createFromInputTypeSpecList($rules);
    }

    public function providesValidMaximumLength()
    {
        return [[1], [2], [100] ];
    }

    /**
     * @param $maxLength
     * @dataProvider providesValidMaximumLength
     */
    public function testValidMaximumLength($maxLength)
    {
        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new MaxLength($maxLength)
            ),
        ];

        $schemaExpectations = [
            'maxLength' => $maxLength,
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testEmptySchema()
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setName('testing');
        $result = $description->toArray();
        $this->assertEquals(['name' => 'testing'], $result);
    }

    public function testMaxInt()
    {
        $maxValue = 45;
        $schemaExpectations = [
            'maximum' => $maxValue,
            'exclusiveMaximum' => false
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetInt(),
                new MaxIntValue($maxValue)
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    public function testPositiveInt()
    {
        $schemaExpectations = [
            'minimum' => 0,
            'exclusiveMinimum' => false,
            'type' => 'integer'
        ];

        $rules = [
            new InputTypeSpec(
                'value',
                new GetInt(),
                new PositiveInt()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

//    public function testSkipIfNull()
//    {
//        $schemaExpectations = [
//            'nullable' => true
//        ];
//        $rules = [
//            new InputParameter(
//                'value',
//                new GetStringOrDefault(null),
//                new SkipIfNull()
//            ),
//        ];
//
//        $this->performSchemaTest($schemaExpectations, $rules);
//    }

    public function testValidDate()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date'
        ];
        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new ValidDate()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }

    public function testValidDateTime()
    {
        $schemaExpectations = [
            'type' => 'string',
            'format' => 'date-time'
        ];
        $rules = [
            new InputTypeSpec(
                'value',
                new GetString(),
                new ValidDatetime()
            ),
        ];

        $this->performSchemaTest($schemaExpectations, $rules);
    }


    /**
     * @param $schemaExpectations
     * @param InputTypeSpec[] $rules
     * @throws OpenApiException
 */
    private function performSchemaTest($schemaExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypeSpecList($rules);

        $this->assertCount(1, $paramDescription);
        $statusDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $statusDescription);
        $schema = $statusDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey(
                $key,
                $schema,
                "Schema missing key [$key]. Schema is " .json_encode($schema)
            );
            $this->assertEquals($value, $schema[$key]);
        }
    }


    private function performFullTest($schemaExpectations, $paramExpectations, $rules)
    {
        $paramDescription = OpenApiV300ParamDescription::createFromInputTypeSpecList($rules);

        $this->assertCount(1, $paramDescription);
        $openApiDescription = $paramDescription[0];

        $this->assertArrayHasKey('schema', $openApiDescription);
        $schema = $openApiDescription['schema'];

        foreach ($schemaExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $schema, "Schema missing key [$key]. Schema is " .json_encode($schema));
            $this->assertEquals($value, $schema[$key]);
        }

        foreach ($paramExpectations as $key => $value) {
            $this->assertArrayHasKey($key, $openApiDescription, "openApiDescription missing key [$key]. Description is " .json_encode($openApiDescription));
            $this->assertEquals($value, $openApiDescription[$key]);
        }
    }

    public function testStringIntEnumAllowed()
    {
        $description = new OpenApiV300ParamDescription('John');
        $description->setEnum(['foo', 5]);
    }


    public function testNonStringNonIntEnumThrows()
    {
        $description = new OpenApiV300ParamDescription('John');
        $this->expectException(OpenApiException::class);
        $description->setEnum(['foo', [123, 456]]);
    }

    /**
     *
     */
    public function testCoverageOnly()
    {
        $description = new ShouldNeverBeCalledParamDescription();
        $trimRule = new Trim();
        $trimRule->updateParamDescription($description);

        $alwaysEndsRule = new AlwaysEndsRule(5);
        $alwaysEndsRule->updateParamDescription($description);
    }

    /**
     * @covers \TypeSpec\ProcessRule\NullIfEmpty
     */
    public function testNullIfEmpty()
    {
        $rule = new NullIfEmpty();

        $description = new OpenApiV300ParamDescription('John');
        $rule->updateParamDescription($description);
        $this->assertTrue($description->getNullAllowed());
    }
}
