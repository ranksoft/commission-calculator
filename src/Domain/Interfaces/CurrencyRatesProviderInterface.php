<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Interfaces;

use CommissionCalculator\Domain\Exceptions\CurrencyRatesException;

interface CurrencyRatesProviderInterface
{
    /**
     * Retrieves all the currency rates.
     *
     * @return array<mixed>
     */
    public function getRates(): array;
}