<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Services;

use CommissionCalculator\Domain\Exceptions\ConfigException;
use CommissionCalculator\Domain\Interfaces\RouterInterface;

class Router implements RouterInterface
{
    const PATH_TO_CONFIG_FILE = __DIR__ . '/../../../config/routes.php';

    /**
     * @var array<mixed>
     */
    private array $routes;

    /**
     * @param string $routesFilePath
     * @throws ConfigException
     */
    public function __construct(string $routesFilePath = self::PATH_TO_CONFIG_FILE)
    {
        if (!file_exists($routesFilePath)) {
            throw new ConfigException("Routes file not found: $routesFilePath");
        }

        $routesData = require_once $routesFilePath;
        if (!is_array($routesData)) {
            throw new ConfigException("Invalid routes format in $routesFilePath");
        }

        $this->routes = $routesData;
    }

    /**
     * @param string $uri
     * @return array<mixed>|null
     */
    public function getRoute(string $uri): ?array
    {
        return $this->routes[$uri] ?? null;
    }
}
