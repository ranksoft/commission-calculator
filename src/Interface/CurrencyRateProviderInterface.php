<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface CurrencyRateProviderInterface
{
    /**
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency): float;
}