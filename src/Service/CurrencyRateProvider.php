<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\CurrencyRateException;
use CommissionCalculator\App\Interface\CurrencyRateProviderInterface;
use CommissionCalculator\App\Interface\CurrencyRatesProviderInterface;

class CurrencyRateProvider implements CurrencyRateProviderInterface
{
    private CurrencyRatesProviderInterface $currencyRatesProvider;

    public function __construct(CurrencyRatesProviderInterface $currencyRatesProvider)
    {
        $this->currencyRatesProvider = $currencyRatesProvider;
    }

    /**
     * @param string $currency
     * @return float
     * @throws CurrencyRateException
     */
    public function getRate(string $currency): float
    {
        $rates = $this->currencyRatesProvider->getRates();
        if (!isset($rates[$currency])) {
            throw new CurrencyRateException('Rate not found for currency:' . $currency);
        }

        return (float)$rates[$currency];
    }
}
