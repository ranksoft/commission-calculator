<?php
declare(strict_types=1);

namespace CommissionCalculator\Infrastructure\DI;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionType;

class DIContainer implements ContainerInterface
{
    private const SERVICES_CONFIG_DIRECTORY_PATH = __DIR__ . '/../../../config/services.php';

    /**
     * @var array<object>
     */
    private array $instances = [];

    /**
     * @var string|array<mixed>
     */
    private string|array $config;

    public function __construct(string $configFile = self::SERVICES_CONFIG_DIRECTORY_PATH)
    {
        $this->config = require $configFile;
    }

    /**
     * @param string $id
     * @param array<mixed> $parameters
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function get(string $id, array $parameters = []): mixed
    {
        if ($this->has($id)) {
            return $this->instances[$id];
        }

        return $this->resolveService($id, $parameters);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * @param string $id
     * @param array<mixed> $parameters
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function resolveService(string $id, array $parameters = []): mixed
    {
        if (!isset($this->config[$id]) && !class_exists($id)) {
            throw new InvalidArgumentException("No entry was found for {$id} identifier.");
        }

        $this->instances[$id] = \is_callable($this->config[$id] ?? null)
            ? $this->config[$id]($this, $parameters)
            : $this->resolveClass($id, $parameters);

        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @param array<mixed> $parameters
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function resolveClass(string $id, array $parameters = []): object
    {
        $definition = $this->config[$id] ?? $id;
        $class = is_array($definition) ? $definition['class'] : $definition;
        $arguments = is_array($definition) ? ($definition['arguments'] ?? []) : [];

        return $this->instantiateClass($class, $arguments, $parameters);
    }

    /**
     * @param string $class
     * @param array<mixed> $arguments
     * @param array<mixed> $parameters
     * @return object
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function instantiateClass(string $class, array $arguments, array $parameters): object
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException("Class {$class} does not exist.");
        }

        $reflectionClass = new ReflectionClass($class);

        if ($reflectionClass->isAbstract() || $reflectionClass->isInterface()) {
            throw new InvalidArgumentException("Cannot instantiate abstract class or interface: {$class}");
        }

        if (!$constructor = $reflectionClass->getConstructor()) {
            return new $class();
        }

        return $reflectionClass->newInstanceArgs(
            $this->resolveDependencies($class, $constructor->getParameters(), $arguments, $parameters)
        );
    }

    /**
     * @param string $class
     * @param array<mixed> $constructorParameters
     * @param array<mixed> $arguments
     * @param array<mixed> $parameters
     * @return array<mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function resolveDependencies(string $class, array $constructorParameters, array $arguments, array $parameters): array
    {
        $dependencies = [];

        foreach ($constructorParameters as $param) {
            $name = $param->getName();
            $type = $param->getType();

            if (!$type) {
                throw new InvalidArgumentException(
                    "The type of the parameter '{$name}' is not specified in the constructor of '{$class}'."
                );
            }

            $dependencies[] = $this->resolveDependency($param, $name, $type, $arguments, $parameters);
        }

        return $dependencies;
    }

    /**
     * @param mixed $param
     * @param string $name
     * @param ReflectionType $type
     * @param array<mixed> $arguments
     * @param array<mixed> $parameters
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|ReflectionException
     */
    private function resolveDependency(mixed $param, string $name, ReflectionType $type, array $arguments, array $parameters): mixed
    {
        if (isset($parameters[$name])) {
            return $parameters[$name];
        }

        if (isset($arguments[$name])) {
            return $this->handleArgumentDependency($arguments[$name]);
        }

        if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
            return $this->get($type->getName());
        }

        if ($param->isDefaultValueAvailable()) {
            return $param->getDefaultValue();
        }

        throw new \InvalidArgumentException("Cannot resolve parameter {$name}.");
    }

    /**
     * @param array<mixed>|callable|string $dependencyConfig
     * @return callable|array<mixed>|string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function handleArgumentDependency(array|callable|string $dependencyConfig): callable|array|string
    {
        return match (true) {
            \is_array($dependencyConfig) => $this->resolveClass($dependencyConfig['class'], $dependencyConfig['arguments']),
            \is_callable($dependencyConfig) => $dependencyConfig($this),
            default => $dependencyConfig
        };
    }
}
