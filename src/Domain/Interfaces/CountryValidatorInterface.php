<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Interfaces;

use CommissionCalculator\Domain\ValueObjects\Country;

interface CountryValidatorInterface
{
    /**
     * Determines if the given country is valid according to specific criteria.
     *
     * @param Country $country
     * @return bool
     */
    public function isValid(Country $country): bool;
}
