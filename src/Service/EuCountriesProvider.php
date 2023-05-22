<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Interface\CountryProviderInterface;
use CommissionCalculator\App\Model\Config;
use Psr\Log\LoggerInterface;

class EuCountriesProvider implements CountryProviderInterface
{
    private Config $config;

    private LoggerInterface $logger;

    public function __construct(
        Config          $config,
        LoggerInterface $logger
    )
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getCountries(): array
    {
        try {
            return $this->config->getEuCountries();
        } catch (ConfigException $exception) {
            $this->logger->error($exception->getMessage());
            return [];
        }
    }
}