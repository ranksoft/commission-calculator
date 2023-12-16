<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CommissionCalculator\Domain\Services\CountryValidator;
use CommissionCalculator\Domain\Interfaces\CountryProviderInterface;
use CommissionCalculator\Domain\ValueObjects\Country;

class CountryValidatorTest extends TestCase
{
    public function testIsValidWithAllowedCountry(): void
    {
        $allowedCountries = ['DE', 'FR', 'IT'];
        $countryProvider = $this->createMock(CountryProviderInterface::class);
        $countryProvider->method('getAllowedCountries')->willReturn($allowedCountries);

        $validator = new CountryValidator($countryProvider);

        $country = new Country('DE');
        $this->assertTrue($validator->isValid($country));
    }

    public function testIsValidWithNotAllowedCountry(): void
    {
        $allowedCountries = ['DE', 'FR', 'IT'];
        $countryProvider = $this->createMock(CountryProviderInterface::class);
        $countryProvider->method('getAllowedCountries')->willReturn($allowedCountries);

        $validator = new CountryValidator($countryProvider);

        $country = new Country('US');
        $this->assertFalse($validator->isValid($country));
    }
}
