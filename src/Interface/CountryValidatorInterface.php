<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface CountryValidatorInterface
{
    /**
     * @param string $country
     * @return bool
     */
    public function isValid(string $country): bool;
}