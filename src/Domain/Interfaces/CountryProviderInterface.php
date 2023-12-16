<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

interface CountryProviderInterface
{
    /**
     * Returns an array of allowed country codes.
     *
     * @return string[]
     */
    public function getAllowedCountries(): array;
}
