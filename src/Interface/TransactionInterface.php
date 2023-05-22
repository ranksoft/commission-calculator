<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface TransactionInterface
{
    public function getBin(): string;

    public function getAmount(): float;

    public function getCurrency(): string;
}