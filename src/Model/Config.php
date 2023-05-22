<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Model;

use CommissionCalculator\App\Exception\ConfigException;

class Config
{
    const PATH_TO_CONFIG_FILE = __DIR__ . '/../../config/config.php';

    /**
     * @var array<mixed>
     */
    private array $config;

    /**
     * @param string $configFilePath
     * @throws ConfigException
     */
    public function __construct(string $configFilePath = self::PATH_TO_CONFIG_FILE)
    {
        if (!\file_exists($configFilePath)) {
            throw new ConfigException("Configuration file not found: $configFilePath");
        }

        $this->config = include $configFilePath;
    }

    /**
     * @param string $key
     * @return mixed
     * @throws ConfigException
     */
    private function get(string $key): mixed
    {
        if (!isset($this->config[$key])) {
            throw new ConfigException("Configuration key not found: $key");
        }

        return $this->config[$key];
    }

    /**
     * @return string[]
     * @throws ConfigException
     */
    public function getEuCountries(): array
    {
        return $this->get('eu_countries');
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public function getApiLayerExchangeUrl(): string
    {
        $apilayer = $this->get('apilayer');
        if (!isset($apilayer['exchange_url'])) {
            throw new ConfigException("Configuration key not found: apilayer -> exchange_url");
        }

        return $apilayer['exchange_url'];
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public function getApiLayerKey(): string
    {
        $apilayer = $this->get('apilayer');
        if (!isset($apilayer['apikey'])) {
            throw new ConfigException("Configuration key not found: apilayer -> apikey");
        }

        return $apilayer['apikey'];
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public function getBinListUrl(): string
    {
        $binlist = $this->get('binlist');
        if (!isset($binlist['url'])) {
            throw new ConfigException("Configuration key not found: binlist -> url");
        }

        return $binlist['url'];
    }

    /**
     * @return float
     * @throws ConfigException
     */
    public function getCommissionDefault(): float
    {
        $commission = $this->get('commission');
        if (!isset($commission['default'])) {
            throw new ConfigException("Configuration key not found: commission -> default");
        }

        return (float)$commission['default'];
    }

    /**
     * @return float
     * @throws ConfigException
     */
    public function getCommissionEu(): float
    {
        $commission = $this->get('commission');
        if (!isset($commission['default'])) {
            throw new ConfigException("Configuration key not found: commission -> eu");
        }

        return (float)$commission['eu'];
    }

    /**
     * @return string
     * @throws ConfigException
     */
    public function getCurrency(): string
    {
        return $this->get('currency');
    }
}