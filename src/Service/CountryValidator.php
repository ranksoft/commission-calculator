<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Interface\CountryProviderInterface;
use CommissionCalculator\App\Interface\CountryValidatorInterface;

class CountryValidator implements CountryValidatorInterface
{
    private CountryProviderInterface $countryProvider;

    public function __construct(CountryProviderInterface $countryProvider)
    {
        $this->countryProvider = $countryProvider;
    }

    public function isValid(string $country): bool
    {
        return in_array($country, $this->countryProvider->getCountries());
    }
}