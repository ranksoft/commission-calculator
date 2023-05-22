<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Model\Config;
use CommissionCalculator\App\Service\BinProvider;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class BinProviderTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private  $configMock;

    /**
     * @var MockObject|ClientInterface
     */
    private  $clientMock;

    /**
     * @var MockObject|RequestFactoryInterface
     */
    private  $requestFactoryMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private  $loggerMock;

    /**
     * @var MockObject|RequestInterface
     */
    private  $requestMock;

    /**
     * @var MockObject|ResponseInterface
     */
    private  $responseMock;

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
     * @throws ClientExceptionInterface
     */
    public function testGetCountryCode(): void
    {
        $bin = '45417360';
        $countryCode = 'JP';
        $responseBody = [
            'country' => [
                'alpha2' => $countryCode,
            ],
        ];
        $this->requestFactoryMock->method('createRequest')->willReturn($this->requestMock);
        $this->clientMock->method('sendRequest')->willReturn($this->responseMock);
        $bodyStream = Utils::streamFor((string)json_encode($responseBody));
        $this->responseMock->method('getBody')->willReturn($bodyStream);
        $provider = new BinProvider($this->configMock, $this->clientMock, $this->requestFactoryMock, $this->loggerMock);
        $responseLookupBody = $provider->lookup($bin);
        $this->assertEquals($responseBody, $responseLookupBody);
    }
}
