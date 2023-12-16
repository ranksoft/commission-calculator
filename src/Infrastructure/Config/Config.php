<?php
declare(strict_types=1);

namespace CommissionCalculator\Infrastructure\Config;

use CommissionCalculator\Domain\Exceptions\ConfigException;

class Config
{
    const PATH_TO_CONFIG_FILE = __DIR__ . '/../../../config/config.php';

    /**
     * @var array<mixed>
     */
    private array $config;

    /**
     * Config constructor.
     *
     * @param string $configFilePath
     * @throws ConfigException
     */
    public function __construct(string $configFilePath = self::PATH_TO_CONFIG_FILE)
    {
        if (!file_exists($configFilePath)) {
            throw new ConfigException("Configuration file not found: $configFilePath");
        }

        $configData = include $configFilePath;
        if (!is_array($configData)) {
            throw new ConfigException("Invalid configuration format in $configFilePath");
        }

        $this->config = $configData;
    }

    /**
     * Get a configuration value by a key path.
     *
     * @param array<string>|string $keyPath
     * @param string|int|float|array<mixed>|null $default
     * @return string|int|float|array<mixed>
     * @throws ConfigException
     */
    public function get(array|string $keyPath, string|int|float|array $default = null): string|int|float|array
    {
        if (!is_array($keyPath)) {
            $keyPath = [$keyPath];
        }

        $value = $this->config;
        foreach ($keyPath as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) {
                if ($default === null) {
                    throw new ConfigException("Configuration key not found: " . implode(' -> ', $keyPath));
                }
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }
}
