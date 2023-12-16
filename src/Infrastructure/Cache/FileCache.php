<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Cache;
use Psr\SimpleCache\CacheInterface;
use DateInterval;
use InvalidArgumentException;

class FileCache implements CacheInterface
{
    private const PATH_TO_CACHE_DIR = __DIR__ . '/../../../var/cache';
    private const FILE_PERMISSIONS = 0770;

    private string $cacheDir;

    public function __construct(string $cacheDir = self::PATH_TO_CACHE_DIR)
    {
        $this->cacheDir = $cacheDir;
        $this->ensureDirectoryExists();
    }

    /**
     * @return void
     */
    private function ensureDirectoryExists(): void
    {
        if (!file_exists($this->cacheDir)) {
            $this->createDirectory();
        }
    }

    /**
     * @return void
     */
    private function createDirectory(): void
    {
        $status = mkdir($this->cacheDir, self::FILE_PERMISSIONS, true);
        if ($status === false && !is_dir($this->cacheDir)) {
            throw new \UnexpectedValueException(sprintf('Cannot create directory at "%s"', $this->cacheDir));
        }
    }

    /**
     * @param string $key
     * @param array<mixed>|string|int|float|null $default
     * @return array<mixed>|float|int|string|null
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->validateKey($key);
        $cacheFile = $this->getFilePath($key);
        if (file_exists($cacheFile)) {
            $cacheFile = file_get_contents($cacheFile);
            if ($cacheFile === false) {
                throw new \UnexpectedValueException(sprintf('Cannot find cache file "%s"', $cacheFile));
            }
            $cacheItem = unserialize($cacheFile);
            if ($cacheItem['ttl'] === null || $cacheItem['ttl'] > time()) {
                return $cacheItem['value'];
            }
        }
        return $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int|DateInterval|null $ttl
     * @return bool
     */
    public function set(string $key, mixed $value, null|int|DateInterval $ttl = null): bool
    {
        $this->validateKey($key);
        $cacheFile = $this->getFilePath($key);
        $cacheItem = [
            'value' => $value,
            'ttl' => $this->calculateTtl($ttl)
        ];
        file_put_contents($cacheFile, serialize($cacheItem));
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        $this->validateKey($key);
        $cacheFile = $this->getFilePath($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
        return true;
    }

    /**
     * @return bool
     */
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*.cache');
        if (is_array($files)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
        return true;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getFilePath(string $key): string
    {
        return $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.cache';
    }

    /**
     * @param DateInterval|int|null $ttl
     * @return int|null
     */
    private function calculateTtl(null|DateInterval|int $ttl): ?int
    {
        if ($ttl === null) {
            return null;
        } elseif ($ttl instanceof DateInterval) {
            return (new \DateTime())->add($ttl)->getTimestamp();
        } elseif (is_int($ttl)) {
            return time() + $ttl;
        } else {
            throw new InvalidArgumentException("Invalid TTL value");
        }
    }

    /**
     * @param string $key
     * @return void
     */
    private function validateKey(string $key): void
    {
        if (!preg_match('/^[a-zA-Z0-9_.]{1,64}$/', $key)) {
            throw new InvalidArgumentException("Invalid cache key: " . $key);
        }
    }

    /**
     * @param iterable<string> $keys
     * @param array<mixed>|string|int|float|null $default
     * @return iterable<mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
    }

    /**
     * @param iterable<mixed> $values
     * @param DateInterval|int|null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }
        return true;
    }

    /**
     * @param iterable<mixed> $keys
     * @return bool
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        $this->validateKey($key);
        $cacheFile = $this->getFilePath($key);
        if (!file_exists($cacheFile)) {
            return false;
        }

        $cacheContent = file_get_contents($cacheFile);
        if ($cacheContent === false) {
            throw new \UnexpectedValueException(sprintf('Cannot find cache file "%s"', $cacheFile));
        }
        $cacheItem = unserialize($cacheContent);
        return $cacheItem['ttl'] === null || $cacheItem['ttl'] > time();
    }
}