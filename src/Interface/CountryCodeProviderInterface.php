<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface CountryCodeProviderInterface
{
    /**
     * @param string $bin
     * @return string
     */
    public function getCountryCode(string $bin): string;
}