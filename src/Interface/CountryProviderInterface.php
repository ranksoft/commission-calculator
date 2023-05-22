<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface CountryProviderInterface
{
    /**
     * @return array<string>
     */
    public function getCountries(): array;
}