<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface   CurrencyRatesProviderInterface
{
    /**
     * @return array<string>
     */
    public function getRates(): array;
}