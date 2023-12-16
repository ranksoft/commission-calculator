<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Interfaces;

use Brick\Money\Money;
use CommissionCalculator\Domain\ValueObjects\Bin;

interface TransactionInterface
{
    public const SCALE = 10;

    /**
     * Retrieves the BIN (Bank Identification Number) of the transaction.
     *
     * @return Bin
     */
    public function getBin(): Bin;

    /**
     * Retrieves the monetary amount of the transaction.
     *
     * @return Money
     */
    public function getMoney(): Money;
}