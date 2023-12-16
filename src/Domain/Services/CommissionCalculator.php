<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Services;

use Brick\Math\BigDecimal;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Currency;
use Brick\Money\Exception\UnknownCurrencyException;
use Brick\Money\Money;
use CommissionCalculator\Domain\Exceptions\CommissionCalculationException;
use CommissionCalculator\Domain\Interfaces\CommissionCalculatorInterface;
use CommissionCalculator\Domain\Interfaces\CountryValidatorInterface;
use CommissionCalculator\Domain\Interfaces\CurrencyRateProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryCodeProviderInterface;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;
use Psr\Log\LoggerInterface;

class CommissionCalculator implements CommissionCalculatorInterface
{
    private const COMMISSION_MULTIPLIER = 100;

    public function __construct(
        private readonly CountryValidatorInterface $countryValidator,
        private readonly CurrencyRateProviderInterface $currencyRatesProvider,
        private readonly CountryCodeProviderInterface $countryCodeProvider,
        private readonly LoggerInterface $logger,
        private readonly float $commissionEu = 0.01,
        private readonly float $commissionDefault = 0.02,
        private readonly string $baseCurrency = 'EUR'
    ) {}

    /**
     * @inheritdoc
     * @throws CommissionCalculationException
     */
    public function calculate(TransactionInterface $transaction): Money
    {
        try {
            $commissionRate = $this->determineCommissionRate($transaction->getBin());
            $amount = $transaction->getMoney();
            $convertedMoney = $this->convertToBaseCurrency($amount);

            return $this->calculateCommission($convertedMoney, $commissionRate, $amount->getCurrency());
        } catch (\Exception $exception) {
            $this->logger->error(
                'Error calculating commission: ' . $exception->getMessage(),
                ['trace' => $exception->getTrace()]
            );
            throw new CommissionCalculationException('Error calculating commission', 0, $exception);
        }
    }

    /**
     * Check if a country associated with a BIN is part of the EU.
     *
     * @param Bin $bin
     * @return bool
     */
    private function isEuCountry(Bin $bin): bool
    {
        $country = $this->countryCodeProvider->getCountry($bin);
        return $this->countryValidator->isValid($country);
    }

    /**
     * Determine the commission rate based on whether the country is in the EU.
     *
     * @param Bin $bin
     * @return float
     */
    private function determineCommissionRate(Bin $bin): float
    {
        return match ($this->isEuCountry($bin)) {
            true => $this->commissionEu,
            default => $this->commissionDefault,
        };
    }

    /**
     * Calculate the commission based on the converted amount and rate.
     *
     * @param BigDecimal $money
     * @param float $rate
     * @param Currency $currency
     * @return Money
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     */
    private function calculateCommission(BigDecimal $money, float $rate, Currency $currency): Money
    {
        $calculatedMoney = $money->multipliedBy(BigDecimal::of($rate))
            ->multipliedBy(BigDecimal::of(self::COMMISSION_MULTIPLIER))
            ->dividedBy(
                self::COMMISSION_MULTIPLIER,
                TransactionInterface::SCALE,
                RoundingMode::UP
            );

        return Money::of(
            (string)$calculatedMoney,
            $currency,
            new CustomContext(TransactionInterface::SCALE),
            RoundingMode::UP
        );
    }

    /**
     * Convert the given Money object to the base currency.
     *
     * @param Money $money
     * @return BigDecimal
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NumberFormatException
     */
    private function convertToBaseCurrency(Money $money): BigDecimal
    {
        if ($money->getCurrency()->getCurrencyCode() === $this->baseCurrency) {
            return $money->getAmount();
        }

        $rate = BigDecimal::of($this->currencyRatesProvider->getRate($money->getCurrency()));
        return $money->getAmount()->dividedBy($rate, TransactionInterface::SCALE, RoundingMode::UP);
    }
}