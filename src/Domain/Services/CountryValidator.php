<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Services;

use CommissionCalculator\Domain\Interfaces\CountryProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryValidatorInterface;
use CommissionCalculator\Domain\ValueObjects\Country;

class CountryValidator implements CountryValidatorInterface
{
    public function __construct(
        private readonly CountryProviderInterface $countryProvider
    ) {}

    /**
     * @param Country $country
     * @return bool
     */
    public function isValid(Country $country): bool
    {
        return in_array($country->getCode(), $this->countryProvider->getAllowedCountries());
    }
}
