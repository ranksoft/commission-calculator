<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Interface\CommissionCalculatorInterface;
use CommissionCalculator\App\Interface\TransactionInterface;

class TransactionProcessor
{
    private CommissionCalculatorInterface $commissionCalculator;

    public function __construct(
        CommissionCalculatorInterface $commissionCalculator
    )
    {
        $this->commissionCalculator = $commissionCalculator;
    }

    /**
     * @param TransactionInterface[] $transactions
     * @return array<mixed>
     */
    public function сommissionСalculation(array $transactions): array
    {
        $commissions = [];
        foreach ($transactions as $transaction) {
            //in production, I would add a check whether it needs to be processed
            $commissions[] = $this->commissionCalculator->calculate($transaction);
        }

        return $commissions;
    }
}