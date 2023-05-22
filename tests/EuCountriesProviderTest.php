<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Service\EuCountriesProvider;
use CommissionCalculator\App\Model\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EuCountriesProviderTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var MockObject|LoggerInterface
     */
    private $loggerMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->configMock = $this->createMock(Config::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }
    /**
     * @return void
     */
    public function testGetCountries(): void
    {
        $this->configMock->method('getEuCountries')->willReturn(['DE', 'FR', 'GB']);
        $provider = new EuCountriesProvider( $this->configMock, $this->loggerMock);
        $this->assertEquals(['DE', 'FR', 'GB'], $provider->getCountries());
    }

    /**
     * @return void
     */
    public function testGetCountriesWhenConfigThrowsException(): void
    {
        $this->configMock->method('getEuCountries')->will($this->throwException(new ConfigException()));
        $provider = new EuCountriesProvider($this->configMock, $this->loggerMock);
        $this->assertEquals([], $provider->getCountries());
    }
}

