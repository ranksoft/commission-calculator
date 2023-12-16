<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\ValueObjects;

class Country
{
    public function __construct(
        private readonly string $code
    ) {}

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
}
