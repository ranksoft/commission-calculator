<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface TransactionRepositoryInterface
{
    /**
     * @return array<TransactionInterface>
     */
    public function getTransactions(): array;
}