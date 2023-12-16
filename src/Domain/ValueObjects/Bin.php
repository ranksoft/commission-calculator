<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\ValueObjects;

class Bin
{
    public function __construct(
        private readonly string $value
    ) {}

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
