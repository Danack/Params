<?php

declare(strict_types=1);

namespace TypeSpec\Value;

/**
 * Class Ordering
 *
 * Represents a set of OrderingElements.
 *
 * e.g. for query where the user wanted to sort by name ascending, and then date
 * descending, they would pass the parameter as "+name,-date" which would be parsed
 * into two OrderElements of:
 *
 * new OrderElement('name', Ordering::ASC)
 * new OrderElement('date', Ordering::DESC)
 *
 */
class Ordering
{
    const ASC = 'asc';
    const DESC = 'desc';

    /** @var \TypeSpec\Value\OrderElement[] */
    private array $orderElements;

    /**
     * Order constructor.
     * @param \TypeSpec\Value\OrderElement[] $orderElements
     */
    public function __construct(array $orderElements)
    {
        $this->orderElements = $orderElements;
    }

    /**
     * @return \TypeSpec\Value\OrderElement[]
     */
    public function getOrderElements(): array
    {
        return $this->orderElements;
    }

    /**
     * @param array<string, string> $carry
     * @param OrderElement $orderElement
     * @return array<string, string>
     */
    private static function reduce(array $carry, \TypeSpec\Value\OrderElement $orderElement)
    {
        $carry[$orderElement->getName()] = $orderElement->getOrder();
        return $carry;
    }

    /**
     * @return array<string, string>
     */
    public function toOrderArray(): array
    {
        return array_reduce($this->orderElements, [self::class, 'reduce'], []);
    }
}
