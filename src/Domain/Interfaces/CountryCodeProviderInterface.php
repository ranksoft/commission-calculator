<?php
declare(strict_types=1);

namespace CommissionCalculator\Domain\Interfaces;

use CommissionCalculator\Domain\Exceptions\BinProviderException;
use CommissionCalculator\Domain\ValueObjects\Bin;
use CommissionCalculator\Domain\ValueObjects\Country;

interface CountryCodeProviderInterface
{
    /**
     * Retrieves the country based on the BIN.
     *
     * @param Bin $bin
     * @return Country
     */
    public function getCountry(Bin $bin): Country;
}