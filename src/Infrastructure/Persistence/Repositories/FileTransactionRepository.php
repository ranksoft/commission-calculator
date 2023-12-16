<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Persistence\Repositories;

use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Currency;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use CommissionCalculator\Domain\Entities\Transaction;
use CommissionCalculator\Domain\Exceptions\RepositoryException;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use CommissionCalculator\Domain\Repositories\TransactionRepositoryInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;
use Psr\Log\LoggerInterface;
use JsonException;

class FileTransactionRepository implements TransactionRepositoryInterface
{
    private const PATH_TO_TRANSACTIONS_FILE = __DIR__ . '/../../../../var/transactions/input.txt';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly string $transactionsFilename = self::PATH_TO_TRANSACTIONS_FILE
    ) {}

    /**
     * @inheritdoc
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws NumberFormatException
     * @throws RepositoryException
     */
    public function getTransactions(): array
    {
        $transactions = [];
        try {
            foreach ($this->getLines() as $line) {
                try {
                    $transactionData = json_decode($line, false, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException $e) {
                    throw new RepositoryException("Invalid transaction data format.", 0, $e);
                }

                $money = Money::of(
                    $transactionData->amount,
                    Currency::of($transactionData->currency),
                    new CustomContext(TransactionInterface::SCALE),
                    RoundingMode::UP
                );
                $transactions[] = new Transaction(new Bin($transactionData->bin), $money);
            }
        } catch (RepositoryException $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }

        return $transactions;
    }

    /**
     * @return iterable<string>
     * @throws RepositoryException
     */
    private function getLines(): iterable
    {
        $file = fopen($this->transactionsFilename, 'rb');
        if (!$file) throw new RepositoryException("Could not open the file: {$this->transactionsFilename}");

        try {
            while (($line = fgets($file)) !== false) {
                yield $line;
            }
        } finally {
            fclose($file);
        }
    }
}
