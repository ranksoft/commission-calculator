<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Repository;

use CommissionCalculator\App\Interface\TransactionInterface;
use CommissionCalculator\App\Interface\TransactionRepositoryInterface;
use CommissionCalculator\App\Model\Transaction;
use Exception;
use Psr\Log\LoggerInterface;

class FileTransactionRepository implements TransactionRepositoryInterface
{
    const PATH_TO_TRANSACTIONS_FILE = __DIR__ . '/../../var/transactions/input.txt';

    private string $transactionsFilename;

    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        string          $filename = self::PATH_TO_TRANSACTIONS_FILE
    )
    {
        $this->logger = $logger;
        $this->transactionsFilename = $filename;
    }

    /**
     * @return TransactionInterface[]
     */
    public function getTransactions(): array
    {
        $transactions = [];
        try {
            foreach ($this->getLines() as $line) {
                $transactionData = \json_decode($line);
                $transaction = new Transaction(
                    (string)$transactionData->bin,
                    (float)$transactionData->amount,
                    (string)$transactionData->currency
                );
                $transactions[] = $transaction;
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $transactions;
    }

    /**
     * @return iterable<string>
     * @throws Exception
     */
    private function getLines(): iterable
    {
        $file = \fopen($this->transactionsFilename, 'r');

        if ($file) {
            while (($line = \fgets($file)) !== false) {
                yield $line;
            }

            \fclose($file);
        } else {
            throw new Exception("Could not open the file!");
        }
    }
}