<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Interface\CurrencyRateProviderInterface;
use CommissionCalculator\App\Interface\TransactionInterface;
use CommissionCalculator\App\Service\BinProvider;
use CommissionCalculator\App\Service\CommissionCalculator;
use CommissionCalculator\App\Service\CountryCodeProvider;
use CommissionCalculator\App\Model\Config;
use CommissionCalculator\App\Service\CountryValidator;
use CommissionCalculator\App\Service\EuCountriesProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CommissionCalculatorTest extends TestCase
{
    /**
     * @var MockObject|TransactionInterface
     */
    private $transactionMock;

    /**
     * @var MockObject|CurrencyRateProviderInterface
     */
    private $currencyRateProviderMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->transactionMock = $this->createMock(TransactionInterface::class);
        $this->currencyRateProviderMock = $this->createMock(CurrencyRateProviderInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     * @throws ConfigException
     */
    public function testCalculate(): void
    {
        $this->transactionMock->method('getBin')->willReturn('45417360');
        $this->transactionMock->method('getAmount')->willReturn(10000.00);
        $this->transactionMock->method('getCurrency')->willReturn('JPY');
        $this->currencyRateProviderMock->method('getRate')->willReturn(149.328);
        $testConfigFile = __DIR__ . '/files/test_config.php';
        $config = new Config($testConfigFile);
        $client = new Client();
        $requestFactory = new HttpFactory();
        $euCountriesProvider = new EuCountriesProvider($config, $this->loggerMock);
        $binProvider = new BinProvider($config, $client, $requestFactory, $this->loggerMock);
        $countryCodeProvider = new CountryCodeProvider($binProvider);
        $countryValidator = new CountryValidator($euCountriesProvider);
        $commissionCalculator = new CommissionCalculator(
            $countryValidator,
            $this->currencyRateProviderMock,
            $countryCodeProvider,
            $config,
            $this->loggerMock
        );
        $this->assertEquals(1.34, $commissionCalculator->calculate($this->transactionMock));
    }
}
