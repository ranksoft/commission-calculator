<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Services;

use CommissionCalculator\Domain\Interfaces\CommissionCalculatorInterface;
use CommissionCalculator\Domain\Interfaces\CommissionFormatterInterface;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use Psr\Log\LoggerInterface;

class TransactionProcessor
{
    /**
     * @param CommissionCalculatorInterface $commissionCalculator
     * @param CommissionFormatterInterface $commissionFormatterInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CommissionCalculatorInterface $commissionCalculator,
        private readonly CommissionFormatterInterface $commissionFormatterInterface,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Processes a collection of transactions and calculates the commission for each.
     *
     * @param TransactionInterface[] $transactions
     * @return array<string>
     */
    public function processTransactions(array $transactions): array
    {
        $commissions = [];
        foreach ($transactions as $transaction) {
            $commission = $this->commissionCalculator->calculate($transaction);
            $commissions[$transaction->getBin()->getValue()] = $this->commissionFormatterInterface->format($commission);

            $this->logger->info(
                "Success calculate: Bin - {$transaction->getBin()->getValue()}, Commission - {$commission}"
            );
        }

        return $commissions;
    }
}
