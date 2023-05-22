<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Exception\CurrencyRateException;
use CommissionCalculator\App\Model\Config;
use CommissionCalculator\App\Service\CurrencyRatesProvider;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class CurrencyRatesProviderTest extends TestCase
{
    /**
     * @var MockObject|Config
     */
    private $configMock;

    /**
     * @var MockObject|ClientInterface
     */
    private $clientMock;

    /**
     * @var MockObject|RequestFactoryInterface
     */
    private $requestFactoryMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @var MockObject|RequestInterface
     */
    private $requestMock;

    /**
     * @var MockObject|ResponseInterface
     */
    private $responseMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->clientMock = $this->createMock(ClientInterface::class);
        $this->requestFactoryMock = $this->createMock(RequestFactoryInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);
    }

    /**
     * @return void
     * @throws ConfigException
     * @throws CurrencyRateException
     * @throws ClientExceptionInterface
     */
    public function testGetRates(): void
    {
        $responseBody = [
            'rates' => [
                'JPY' => 149.328,
                'EUR' => 1,
            ],
        ];

        $this->requestMock->method('withHeader')->willReturn($this->requestMock);
        $this->requestFactoryMock->method('createRequest')->willReturn($this->requestMock);
        $this->clientMock->method('sendRequest')->willReturn($this->responseMock);
        $bodyStream = Utils::streamFor((string)json_encode($responseBody));
        $this->responseMock->method('getBody')->willReturn($bodyStream);
        $provider = new CurrencyRatesProvider($this->configMock, $this->clientMock, $this->requestFactoryMock, $this->loggerMock);
        $rates = $provider->getRates();
        $this->assertEquals($responseBody['rates'], $rates);
    }
}