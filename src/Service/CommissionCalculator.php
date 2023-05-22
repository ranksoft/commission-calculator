<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Interface\CommissionCalculatorInterface;
use CommissionCalculator\App\Interface\CountryCodeProviderInterface;
use CommissionCalculator\App\Interface\CountryValidatorInterface;
use CommissionCalculator\App\Interface\CurrencyRateProviderInterface;
use CommissionCalculator\App\Interface\TransactionInterface;
use CommissionCalculator\App\Model\Config;
use Exception;
use Psr\Log\LoggerInterface;

class CommissionCalculator implements CommissionCalculatorInterface
{
    private CountryValidatorInterface $countryValidator;

    private CurrencyRateProviderInterface $currencyRatesProvider;

    private CountryCodeProviderInterface $countryCodeProvider;

    private Config $config;

    private LoggerInterface $logger;

    public function __construct(
        CountryValidatorInterface     $countryValidator,
        CurrencyRateProviderInterface $currencyRatesProvider,
        CountryCodeProviderInterface  $countryCodeProvider,
        Config                        $config,
        LoggerInterface               $logger,
    )
    {
        $this->countryValidator = $countryValidator;
        $this->currencyRatesProvider = $currencyRatesProvider;
        $this->countryCodeProvider = $countryCodeProvider;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param TransactionInterface $transaction
     * @return float|int
     * @throws ConfigException
     */
    public function calculate(TransactionInterface $transaction): float|int
    {
        try {
            $countryCode = $this->countryCodeProvider->getCountryCode($transaction->getBin());
            // The country matches the list of countries from the contra provider
            $isValid = $this->countryValidator->isValid($countryCode);
            $amountFixed = $this->calculateAmountFixed($transaction);
            $commission = $this->calculateCommission($amountFixed, $isValid);

            //Rounding up with two decimal places
            return \ceil($commission * 100) / 100;
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @param TransactionInterface $transaction
     * @return float
     * @throws ConfigException
     */
    private function calculateAmountFixed(TransactionInterface $transaction): float
    {
        $rate = $this->currencyRatesProvider->getRate($transaction->getCurrency());
        $amountFixed = $transaction->getAmount();
        if ($transaction->getCurrency() !== $this->config->getCurrency() || $rate > 0) {
            $amountFixed = $transaction->getAmount() / $rate;
        }

        return $amountFixed;
    }

    /**
     * @param float $amountFixed
     * @param bool $isValid
     * @return float
     * @throws ConfigException
     */
    private function calculateCommission(float $amountFixed, bool $isValid): float
    {
        //apply commission rates
        return $amountFixed * ($isValid ? $this->config->getCommissionEu() : $this->config->getCommissionDefault());
    }
}