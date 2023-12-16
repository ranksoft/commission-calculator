<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Repositories;

use CommissionCalculator\Domain\Interfaces\TransactionInterface;

interface TransactionRepositoryInterface
{
    /**
     * Retrieves all transactions from the repository.
     *
     * @return TransactionInterface[]
     */
    public function getTransactions(): array;
}