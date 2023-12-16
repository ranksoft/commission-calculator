<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CommissionCalculator\Infrastructure\Services\EuCountriesProvider;

class EuCountriesProviderTest extends TestCase
{
    /**
     */
    public function testGetAllowedCountries(): void
    {
        $euCountries = ['DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR'];
        $provider = new EuCountriesProvider($euCountries);

        $result = $provider->getAllowedCountries();

        $this->assertSame($euCountries, $result);
    }
}
