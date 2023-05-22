<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\CurrencyRateException;
use CommissionCalculator\App\Exception\CurrencyRatesException;
use CommissionCalculator\App\Interface\CurrencyRateProviderInterface;
use CommissionCalculator\App\Interface\CurrencyRatesProviderInterface;
use Psr\Log\LoggerInterface;

class CurrencyRateProvider implements CurrencyRateProviderInterface
{
    private CurrencyRatesProviderInterface $currencyRatesProvider;

    private LoggerInterface $logger;

    public function __construct(
        CurrencyRatesProviderInterface $currencyRatesProvider,
        LoggerInterface $logger
    )
    {
        $this->currencyRatesProvider = $currencyRatesProvider;
        $this->logger = $logger;
    }

    /**
     * @param string $currency
     * @return float
     * @throws CurrencyRateException
     */
    public function getRate(string $currency): float
    {
        $rates = [];
        try {
            $rates = $this->currencyRatesProvider->getRates();
        } catch (CurrencyRatesException $exception) {
            $this->logger->error($exception->getMessage());
        }

        if (!isset($rates[$currency])) {
            throw new CurrencyRateException('Rate not found for currency:' . $currency);
        }

        return (float)$rates[$currency];
    }
}
