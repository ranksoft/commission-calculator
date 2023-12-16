<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Api;

use Brick\Money\Currency;
use CommissionCalculator\Domain\Interfaces\CurrencyRatesProviderInterface;
use CommissionCalculator\Domain\Interfaces\CurrencyRateProviderInterface;
use CommissionCalculator\Domain\Exceptions\CurrencyRateException;
use Psr\Log\LoggerInterface;

class CurrencyRateProvider implements CurrencyRateProviderInterface
{
    public function __construct(
        private readonly CurrencyRatesProviderInterface $currencyRatesProvider,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @inheritdoc
     * @throws CurrencyRateException
     */
    public function getRate(Currency $currency): float
    {
        try {
            $rates = $this->currencyRatesProvider->getRates();
            $currencyCode = $currency->getCurrencyCode();

            if (!isset($rates[$currencyCode])) {
                throw new CurrencyRateException("Rate not found for currency: {$currencyCode}");
            }

            return (float)$rates[$currencyCode];
        } catch (CurrencyRateException $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }
}