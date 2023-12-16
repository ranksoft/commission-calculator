<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Interfaces;

use Brick\Money\Currency;
use CommissionCalculator\Domain\Exceptions\CurrencyRateException;

interface CurrencyRateProviderInterface
{
    /**
     * Retrieves the exchange rate for the specified currency.
     *
     * @param Currency $currency
     * @return float
     */
    public function getRate(Currency $currency): float;
}