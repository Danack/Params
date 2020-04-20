<?php

declare(strict_types=1);

namespace Params\OpenApi;

use Params\Exception\OpenApiException;
use function Params\array_value_exists;

class OpenApiV300ParamDescription implements ParamDescription
{
    private string $name;

    private ?string $type = null;

    private ?string $format = null;

    private ?string $description = null;

    /**
     * @var ?array<mixed>
     */
    private $enumValues = null;

    private ?bool $required = null;

    /** @var int|float|null */
    private $minimum = null;

    /** @var int|float|null */
    private $maximum = null;

    private ?int $maxLength = null;

    private ?int $minLength = null;

    private ?int $minItems;

    private ?int $maxItems;


    /**
     * @var mixed
     */
    private $default = null;

    private ?bool $exclusiveMaximum = null;

    private ?bool $exclusiveMinimum = null;

    private ?bool $nullAllowed = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Creates a set of Parameter descriptions according to the
     * OpenApi 3.0.0 spec
     *
     * https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md
     * @param \Params\InputParameter[] $allRules
     * @return self[]
     * @throws OpenApiException
     */
    public static function createFromRules($allRules)
    {
        $ruleDescriptions = [];

        foreach ($allRules as $rules) {
            $description = new self($rules->getInputName());

            $firstRule = $rules->getExtractRule();
            $firstRule->updateParamDescription($description);

            foreach ($rules->getProcessRules() as $subsequentRules) {
                /** @var $rule \Params\Rule */
                $subsequentRules->updateParamDescription($description);
            }

            $ruleDescriptions[] = $description->toArray();
        }

        return $ruleDescriptions;
    }

    public function toArray(): array
    {
        $array = [];

        $array['name'] = $this->name;

        if ($this->required !== null) {
            $array['required'] = $this->required;
        }

        $schema = $this->generateSchema();

        if (count($schema) !== 0) {
            $array['schema'] = $schema;
        }

        return $array;
    }


    private function generateSchema(): array
    {
        $schema = [];

        if ($this->minimum !== null) {
            $schema['minimum'] = $this->minimum;
        }
        if ($this->maximum !== null) {
            $schema['maximum'] = $this->maximum;
        }
        if ($this->default !== null) {
            $schema['default'] = $this->default;
        }
        if ($this->type !== null) {
            $schema['type'] = $this->type;
        }
        if ($this->format !== null) {
            $schema['format'] = $this->format;
        }
        if ($this->enumValues !== null) {
            $schema['enum'] = $this->enumValues;
        }
        if ($this->minLength !== null) {
            $schema['minLength'] = $this->minLength;
        }
        if ($this->maxLength !== null) {
            $schema['maxLength'] = $this->maxLength;
        }

        if ($this->exclusiveMaximum !== null) {
            $schema['exclusiveMaximum'] = $this->exclusiveMaximum;
        }
        if ($this->exclusiveMinimum !== null) {
            $schema['exclusiveMinimum'] = $this->exclusiveMinimum;
        }
        if ($this->nullAllowed !== null) {
            $schema['nullable'] = $this->nullAllowed;
        }

        // done
        // maximum
        // minimum
        // maxLength
        // minLength
        // required
        //    format - See Data Type Formats for further details. While relying on JSON Schema's defined formats, the OAS offers a few additional predefined formats.
//default - The default value represents
//    type - Value MUST be a string. Multiple types via an array are not supported.

        //TODO

//title
//multipleOf

//exclusiveMaximum

//exclusiveMinimum

//pattern (This string SHOULD be a valid regular expression, according to the ECMA 262 regular expression dialect)
//maxItems
//minItems
//uniqueItems
//maxProperties
//minProperties




//    allOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    oneOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    anyOf - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    not - Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    items - Value MUST be an object and not an array. Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema. items MUST be present if the type is array.
//properties - Property definitions MUST be a Schema Object and not a standard JSON Schema (inline or referenced).
//    additionalProperties - Value can be boolean or object. Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
//    description - CommonMark syntax MAY be used for rich text representation.


        return $schema;
    }


    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setIn(string $in): void
    {
        // TODO: Implement setIn() method.
        throw new \Exception("setIn not implemented yet.");
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return bool|null
     */
    public function getExclusiveMaximum(): ?bool
    {
        return $this->exclusiveMaximum;
    }

    /**
     * @return bool|null
     */
    public function getExclusiveMinimum(): ?bool
    {
        return $this->exclusiveMinimum;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function setSchema(string $schema): void
    {
        // TODO: Implement setSchema() method.
        throw new \Exception("setSchema not implemented yet.");
    }

    public function setType(string $type): void
    {
        $knownTypes = [
           'string',// (this includes dates and files)
           'float',
           'number',
           'integer',
           'boolean',
           'array',
           'object',
        ];

        if (array_value_exists($knownTypes, $type) === false) {
            throw new OpenApiException("Type [$type] is not known for the OpenApi spec.");
        }

        $this->type = $type;
    }

    public function setFormat(string $format): void
    {
        if ($this->type === 'number') {
            $knownFormats = [
                'float',  // Floating-point numbers.
                'double', // floating-point numbers with double precision.
            ];

            if (array_value_exists($knownFormats, $format) === false) {
                throw new OpenApiException("Format [$format] is not known for type 'number' the OpenApi spec.");
            }
        }
        else if ($this->type === 'integer') {
            $knownFormats = [
                'int32', // Signed 32-bit integers (commonly used integer type).
                'int64', // Signed 64-bit integers (long type).
            ];

            if (array_value_exists($knownFormats, $format) === false) {
                throw new OpenApiException("Format [$format] is not known for type 'integer' the OpenApi spec.");
            }
        }


        $this->format = $format;
    }

    public function setAllowEmptyValue(bool $allowEmptyValue): void
    {
        // TODO: Implement setAllowEmptyValue() method.
        throw new \Exception("setAllowEmptyValue not implemented yet.");
    }

    public function getItems(): ItemsObject
    {
        // TODO: Implement getItems() method.
        throw new \Exception("getItems not implemented yet.");
    }

    public function setItems(ItemsObject $itemsObject): void
    {
        // TODO: Implement setItems() method.
        throw new \Exception("setItems not implemented yet.");
    }

    public function setCollectionFormat(string $collectionFormat): void
    {
//        simple
        // CSV

        // TODO - version 3, replaces collectionFormat
        // with style = simple
//        throw new \Exception("setCollectionFormat not implemented yet.");
    }

    public function setDefault($default): void
    {
        $this->default = $default;
    }

    /**
     * @param int|float|null $maximum
     */
    public function setMaximum($maximum): void
    {
        $this->maximum = $maximum;
    }

    public function setExclusiveMaximum(bool $exclusiveMaximum): void
    {
        $this->exclusiveMaximum = $exclusiveMaximum;
    }

    public function setMinimum($minimum): void
    {
        $this->minimum = $minimum;
    }

    public function setExclusiveMinimum(bool $exclusiveMinimum): void
    {
        $this->exclusiveMinimum = $exclusiveMinimum;
    }

    public function setMaxLength(int $maxLength): void
    {
        if ($maxLength <= 0) {
            throw new OpenApiException("Max length must be greater than 0");
        }
        $this->maxLength = $maxLength;
    }


    public function setMinLength(int $minLength): void
    {
        if ($minLength <= 0) {
            throw new OpenApiException("Min length must be at least 0");
        }
        $this->minLength = $minLength;
    }

    public function setPattern(string $pattern): void
    {
        // pattern: '^\d{3}-\d{2}-\d{4}$'
        // TODO: Implement setPattern() method.
        throw new \Exception("setPattern not implemented yet.");
    }

    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }

    public function setMinItems(int $minItems): void
    {
        $this->minItems = $minItems;
    }

    public function setNullAllowed(bool $allowed): void
    {
        $this->nullAllowed = $allowed;
    }

    public function setUniqueItems(bool $uniqueItems): void
    {
        // TODO: Implement setUniqueItems() method.
        throw new \Exception("setUniqueItems not implemented yet.");
    }

    /**
     * @param array<mixed> $enumValues
     * @throws OpenApiException
     */
    public function setEnum(array $enumValues): void
    {
        // TODO - this is technically incorrect - specs say enum can be any type
        foreach ($enumValues as $enumValue) {
            if (is_string($enumValue) !== true) {
                throw new OpenApiException("All enum values must be strings.");
            }
        }

        $this->enumValues = $enumValues;
    }

    public function getEnum(): ?array
    {
        return $this->enumValues;
    }


    public function setMultipleOf($multiple): void
    {
        // TODO: Implement setMultipleOf() method.
        throw new \Exception("setMultipleOf not implemented yet.");
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @return ?array<mixed>
     */
    public function getEnumValues()
    {
        return $this->enumValues;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @return float|int|null
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @return float|int|null
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    public function isExclusiveMaximum(): ?bool
    {
        return $this->exclusiveMaximum;
    }

    public function isExclusiveMinimum(): ?bool
    {
        return $this->exclusiveMinimum;
    }

    public function getNullAllowed(): ?bool
    {
        return $this->nullAllowed;
    }


    public function getMinItems(): ?int
    {
        return $this->minItems;
    }

    public function getMaxItems(): ?int
    {
        return $this->maxItems;
    }

// examples:
//  oneId:
//    summary: Example of a single ID
//    value: [5]   # ?ids=5
//  multipleIds:
//    summary: Example of multiple IDs
//    value: [1, 5, 7]   # ?ids=1,5,7
}
