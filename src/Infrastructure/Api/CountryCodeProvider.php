<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Api;

use CommissionCalculator\Domain\Interfaces\BinProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryCodeProviderInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;
use CommissionCalculator\Domain\Exceptions\BinProviderException;
use CommissionCalculator\Domain\ValueObjects\Country;

class CountryCodeProvider implements CountryCodeProviderInterface
{
    public function __construct(
        private readonly BinProviderInterface $binProvider
    ) {}

    /**
     * @inheritdoc
     * @throws BinProviderException
     */
    public function getCountry(Bin $bin): Country
    {
        try {
            $binData = $this->binProvider->getBinData($bin);

            if (!isset($binData['country']['alpha2'])) {
                throw new BinProviderException('Country alpha2 not found for bin: ' . $bin->getValue());
            }

            return new Country($binData['country']['alpha2']);
        } catch (\Exception $exception) {
            throw new BinProviderException($exception->getMessage(), 0, $exception);
        }
    }
}
