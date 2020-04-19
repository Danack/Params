<?php

declare(strict_types=1);

namespace Params\OpenApi;

/**
 * Used for testing that Rules that shouldn't affect
 * the parameter descriptions.
 * @codeCoverageIgnore
 */
class ShouldNeverBeCalledParamDescription implements ParamDescription
{
    public function setName(string $name): void
    {
        throw new \Exception("setName should not be called.");
    }

    public function setIn(string $in): void
    {
        throw new \Exception("setIn should not be called.");
    }

    public function setDescription(string $description): void
    {
        throw new \Exception("setDescription should not be called.");
    }

    public function setRequired(bool $required): void
    {
        throw new \Exception("setRequired should not be called.");
    }

    public function setSchema(string $schema): void
    {
        throw new \Exception("setSchema should not be called.");
    }

    public function setType(string $type): void
    {
        throw new \Exception("setType should not be called.");
    }

    public function setFormat(string $format): void
    {
        throw new \Exception("setFormat should not be called.");
    }

    public function setAllowEmptyValue(bool $allowEmptyValue): void
    {
        throw new \Exception("setAllowEmptyValue should not be called.");
    }

    public function getItems(): ItemsObject
    {
        throw new \Exception("getItems should not be called.");
    }

    public function setItems(ItemsObject $itemsObject): void
    {
        throw new \Exception("setItems should not be called.");
    }

    public function setCollectionFormat(string $collectionFormat): void
    {
        throw new \Exception("setCollectionFormat should not be called.");
    }

    public function setDefault($default): void
    {
        throw new \Exception("setDefault should not be called.");
    }

    public function setMaximum($maximum): void
    {
        throw new \Exception("setMaximum should not be called.");
    }

    public function setExclusiveMaximum(bool $exclusiveMaximum): void
    {
        throw new \Exception("setExclusiveMaximum should not be called.");
    }

    public function setMinimum($minimum): void
    {
        throw new \Exception("setMinimum should not be called.");
    }

    public function setExclusiveMinimum(bool $exclusiveMinimum): void
    {
        throw new \Exception("setExclusiveMinimum should not be called.");
    }

    public function setMaxLength(int $maxLength): void
    {
        throw new \Exception("setMaxLength should not be called.");
    }

    public function setMinLength(int $minLength): void
    {
        throw new \Exception("setMinLength should not be called.");
    }

    public function setPattern(string $pattern): void
    {
        throw new \Exception("setPattern should not be called.");
    }

    public function setMaxItems(int $maxItems): void
    {
        throw new \Exception("setMaxItems should not be called.");
    }

    public function setMinItems(int $minItems): void
    {
        throw new \Exception("setMinItems should not be called.");
    }

    public function setNullAllowed(): void
    {
        throw new \Exception("setNullAllowed should not be called.");
    }

    public function setUniqueItems(bool $uniqueItems): void
    {
        throw new \Exception("setUniqueItems should not be called.");
    }

    /**
     * @param array<mixed> $enumValues
     * @throws \Exception
     */
    public function setEnum(array $enumValues): void
    {
        throw new \Exception("setEnum should not be called.");
    }

    public function setMultipleOf($multiple): void
    {
        throw new \Exception("setMultipleOf should not be called.");
    }

    public function getDescription(): ?string
    {
        throw new \Exception("getDescription should not be called.");
    }
}
