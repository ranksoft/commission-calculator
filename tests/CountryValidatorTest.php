<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Service\CountryValidator;
use CommissionCalculator\App\Interface\CountryProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CountryValidatorTest extends TestCase
{
    /**
     * @var MockObject|CountryProviderInterface
     */
    private $countryProviderMock;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->countryProviderMock = $this->createMock(CountryProviderInterface::class);
    }

    /**
     * @return void
     */
    public function testIsValid(): void
    {
        $this->countryProviderMock->method('getCountries')->willReturn(['FR', 'GB', 'DE']);
        $manager = new CountryValidator($this->countryProviderMock);
        $this->assertTrue($manager->isValid('FR'));
        $this->assertFalse($manager->isValid('US'));
    }
}
