<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

use Brick\Money\Money;

interface CommissionFormatterInterface
{
    /**
     * Formats a Money object into a decimal string.
     *
     * @param Money $commission
     * @return string
     */
    public function format(Money $commission): string;
}
