<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface CommissionCalculatorInterface
{
    /**
     * @param TransactionInterface $transaction
     * @return float|int
     */
    public function calculate(TransactionInterface $transaction): float|int;
}