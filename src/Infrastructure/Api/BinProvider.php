<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Api;

use CommissionCalculator\Domain\Exceptions\RateLimitExceededException;
use CommissionCalculator\Domain\Interfaces\BinProviderInterface;
use CommissionCalculator\Domain\ValueObjects\Bin;
use CommissionCalculator\Domain\Exceptions\BinProviderException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class BinProvider implements BinProviderInterface
{
    private const CACHE_KEY_PREFIX = 'bin_data_';
    private const CACHE_TTL = 86400;

    public function __construct(
        private readonly string $binListUrl,
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache
    ) {}

    /**
     * @inheritdoc
     * @throws BinProviderException
     * @throws InvalidArgumentException|ClientExceptionInterface
     */
    public function getBinData(Bin $bin): array
    {
        $cacheKey = $this->getCacheKey($bin);

        try {
            if ($this->cache->has($cacheKey)) {
                return $this->cache->get($cacheKey);
            }

            $data = $this->fetchDataFromApi($bin);

            if (empty($data['country'])) {
                throw new BinProviderException('Country in response is empty');
            }

            $this->cacheData($cacheKey, $data);

            return $data;
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
            throw new BinProviderException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param Bin $bin
     * @return array<mixed>
     * @throws BinProviderException
     * @throws ClientExceptionInterface
     * @throws RateLimitExceededException
     */
    private function fetchDataFromApi(Bin $bin): array
    {
        $request = $this->requestFactory
            ->createRequest('GET', $this->binListUrl . $bin->getValue())
            ->withHeader('Accept-Version', '3');
        $response = $this->client->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() === 429) {
            throw new RateLimitExceededException("Rate limit exceeded. Please try again later.");
        }

        if ($response->getStatusCode() !== 200 || !$responseData) {
            throw new BinProviderException("Error while retrieving BIN data.");
        }

        return $responseData;
    }

    /**
     * @param string $key
     * @param array<mixed> $data
     * @return void
     * @throws InvalidArgumentException
     */
    private function cacheData(string $key, array $data): void
    {
        $this->cache->set($key, $data, self::CACHE_TTL);
    }

    /**
     * @param Bin $bin
     * @return string
     */
    private function getCacheKey(Bin $bin): string
    {
        return self::CACHE_KEY_PREFIX . $bin->getValue();
    }
}