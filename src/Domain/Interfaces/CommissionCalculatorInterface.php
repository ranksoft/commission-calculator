<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

use Brick\Money\Money;

interface CommissionCalculatorInterface
{
    /**
     * Interface for commission calculation services.
     *
     * @param TransactionInterface $transaction
     * @return Money
     */
    public function calculate(TransactionInterface $transaction): Money;
}