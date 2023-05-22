<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\BinException;
use CommissionCalculator\App\Interface\BinProviderInterface;
use CommissionCalculator\App\Interface\CountryCodeProviderInterface;

class CountryCodeProvider implements CountryCodeProviderInterface
{
    private BinProviderInterface $binProvider;

    public function __construct(
        BinProviderInterface $binProvider
    )
    {
        $this->binProvider = $binProvider;
    }

    /**
     * @param string $bin
     * @return string
     * @throws BinException
     */
    public function getCountryCode(string $bin): string
    {
        $binResults = $this->binProvider->lookup($bin);
        if (!isset($binResults['country'])) {
            throw new BinException('Country in response are empty');
        }
        if (!isset($binResults['country']['alpha2'])) {
            throw new BinException('Country alpha2 not found for bin:' . $bin);
        }

        return $binResults['country']['alpha2'];
    }
}