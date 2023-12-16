<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Entities;

use Brick\Money\Money;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;

final class Transaction implements TransactionInterface
{
    public function __construct(
        private readonly Bin $bin,
        private readonly Money $money
    ) {}

    /**
     * @inheritdoc
     */
    public function getBin(): Bin
    {
        return $this->bin;
    }

    /**
     * @inheritdoc
     */
    public function getMoney(): Money
    {
        return $this->money;
    }
}
