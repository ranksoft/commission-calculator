<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Services;

use CommissionCalculator\Domain\Interfaces\CountryProviderInterface;

class EuCountriesProvider implements CountryProviderInterface
{
    /**
     * @param array<string> $euCountries
     */
    public function __construct(
        private readonly array $euCountries
    ) {}

    /**
     * @inheritdoc
     */
    public function getAllowedCountries(): array
    {
        return $this->euCountries;
    }
}
