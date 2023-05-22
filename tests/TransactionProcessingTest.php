<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Interface\CountryCodeProviderInterface;
use CommissionCalculator\App\Interface\CurrencyRateProviderInterface;
use CommissionCalculator\App\Model\Config;
use CommissionCalculator\App\Service\CountryValidator;
use CommissionCalculator\App\Service\TransactionProcessor;
use CommissionCalculator\App\Service\CommissionCalculator;
use CommissionCalculator\App\Service\EuCountriesProvider;
use CommissionCalculator\App\Repository\FileTransactionRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TransactionProcessingTest extends TestCase
{
    /**
     * @var MockObject|Config
     */
    private $config;

    /**
     * @var MockObject|CountryCodeProviderInterface
     */
    private $countryCodeProviderMock;

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
        $this->config = $this->createMock(Config::class);
        $this->countryCodeProviderMock = $this->createMock(CountryCodeProviderInterface::class);
        $this->currencyRateProviderMock = $this->createMock(CurrencyRateProviderInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     */
    public function testTransactionProcessing(): void
    {
        $this->config->method('getEuCountries')->willReturn(['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK']);
        $this->config->method('getCurrency')->willReturn('EUR');
        $this->config->method('getCommissionEu')->willReturn(0.01);
        $this->config->method('getCommissionDefault')->willReturn(0.02);
        $this->countryCodeProviderMock->method('getCountryCode')->willReturn('JP');
        $this->currencyRateProviderMock->method('getRate')->willReturn(149.328);
        $euCountriesProvider = new EuCountriesProvider($this->config, $this->loggerMock);
        $countryValidator = new CountryValidator($euCountriesProvider);
        $commissionCalculator = new CommissionCalculator($countryValidator, $this->currencyRateProviderMock, $this->countryCodeProviderMock, $this->config, $this->loggerMock);
        $testTransactionsInputFilePath = __DIR__ . '/files/test_transaction_processing_input_file.txt';
        $transactionRepository = new FileTransactionRepository($this->loggerMock, $testTransactionsInputFilePath);
        $transactionProcessor = new TransactionProcessor($commissionCalculator);
        $transactions = $transactionRepository->getTransactions();
        $commissions = $transactionProcessor->сommissionСalculation($transactions);
        $this->assertIsArray($commissions);
        $this->assertNotEmpty($commissions);
        $this->assertContains(1.34, $commissions);
    }
}
