<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Exception\BinException;
use CommissionCalculator\App\Interface\BinProviderInterface;
use CommissionCalculator\App\Service\CountryCodeProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CountryCodeProviderTest extends TestCase
{
    /**
     * @var MockObject|BinProviderInterface
     */
    private $binProviderMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->binProviderMock = $this->createMock(BinProviderInterface::class);
    }

    /**
     * @return void
     * @throws BinException
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
        $this->binProviderMock->method('lookup')->willReturn($responseBody);
        $countryCodeProvider = new CountryCodeProvider($this->binProviderMock);
        $countryCode = $countryCodeProvider->getCountryCode($bin);
        $this->assertEquals($countryCode, $countryCode);
    }
}
