<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\CurrencyRateException;
use CommissionCalculator\App\Interface\CurrencyRatesProviderInterface;
use CommissionCalculator\App\Service\CurrencyRateProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CurrencyRateProviderTest extends TestCase
{
    /**
     * @var MockObject|CurrencyRatesProviderInterface
     */
    private $currencyRatesProviderMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private  $loggerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->currencyRatesProviderMock = $this->createMock(CurrencyRatesProviderInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    /**
     * @return void
     * @throws CurrencyRateException
     */
    public function testGetRate(): void
    {
        $currency = 'JPY';
        $rates = [
            'JPY' => 149.328
        ];
        $this->currencyRatesProviderMock->method('getRates')->willReturn($rates);
        $provider = new CurrencyRateProvider($this->currencyRatesProviderMock, $this->loggerMock);
        $rate = $provider->getRate($currency);
        $this->assertEquals($rates['JPY'], $rate);
    }
}