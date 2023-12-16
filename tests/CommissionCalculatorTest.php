<?php
declare(strict_types=1);

use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Money\Exception\UnknownCurrencyException;
use CommissionCalculator\Domain\Exceptions\CommissionCalculationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use CommissionCalculator\Domain\Services\CommissionCalculator;
use CommissionCalculator\Domain\Interfaces\CountryValidatorInterface;
use CommissionCalculator\Domain\Interfaces\CurrencyRateProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryCodeProviderInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use Brick\Money\Money;
use Psr\Log\NullLogger;

class CommissionCalculatorTest extends TestCase
{
    private MockObject|CountryValidatorInterface $countryValidator;
    private MockObject|CurrencyRateProviderInterface $currencyRatesProvider;
    private MockObject|CommissionCalculator $calculator;

    protected function setUp(): void
    {
        $this->countryValidator = $this->createMock(CountryValidatorInterface::class);
        $this->currencyRatesProvider = $this->createMock(CurrencyRateProviderInterface::class);

        $this->calculator = new CommissionCalculator(
            $this->countryValidator,
            $this->currencyRatesProvider,
            $this->createMock(CountryCodeProviderInterface::class),
            new NullLogger(),
            0.01,
            0.02,
            'EUR'
        );
    }

    /**
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws CommissionCalculationException
     * @throws NumberFormatException
     */
    public function testCalculateCommission(): void
    {
        $transactionMock = $this->createMock(TransactionInterface::class);
        $transactionMock->method('getBin')->willReturn(new Bin('123456'));
        $transactionMock->method('getMoney')->willReturn(Money::of('50', 'EUR'));
        $this->countryValidator->method('isValid')->willReturn(true);

        $commission = $this->calculator->calculate($transactionMock);

        $this->assertEquals('0.5000000000', (string)$commission->getAmount());
    }

    /**
     * @throws RoundingNecessaryException
     * @throws UnknownCurrencyException
     * @throws CommissionCalculationException
     * @throws NumberFormatException
     */
    public function testCalculateCommissionWithNotValid(): void
    {
        $transactionMock = $this->createMock(TransactionInterface::class);
        $transactionMock->method('getBin')->willReturn(new Bin('654321'));
        $transactionMock->method('getMoney')->willReturn(Money::of('50', 'USD'));
        $this->countryValidator->method('isValid')->willReturn(false);
        $this->currencyRatesProvider->method('getRate')->willReturn(0.20);

        $commission = $this->calculator->calculate($transactionMock);

        $this->assertEquals('5.0000000000', (string)$commission->getAmount());
    }
}
