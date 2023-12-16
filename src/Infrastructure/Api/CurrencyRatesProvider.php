<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Api;

use CommissionCalculator\Domain\Interfaces\CurrencyRatesProviderInterface;
use CommissionCalculator\Domain\Exceptions\CurrencyRatesException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CurrencyRatesProvider implements CurrencyRatesProviderInterface
{
    private const CACHE_KEY = 'currency_rates';
    private const CACHE_TTL = 3600;

    public function __construct(
        private readonly string $exchangeUrl,
        private readonly string $apiKey,
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache
    ) {}

    /**
     * @inheritdoc
     * @throws CurrencyRatesException
     */
    public function getRates(): array
    {
        try {
            if ($this->cache->has(self::CACHE_KEY)) {
                $this->logger->info("Using cached currency rates.");
                return $this->cache->get(self::CACHE_KEY);
            }

            $responseData = $this->fetchRatesFromApi();
            $this->cacheRates($responseData['rates']);

            return $responseData['rates'];
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
            throw new CurrencyRatesException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * @return array<mixed>
     * @throws CurrencyRatesException
     * @throws ClientExceptionInterface
     */
    private function fetchRatesFromApi(): array
    {
        $request = $this->requestFactory->createRequest('GET', $this->exchangeUrl)
            ->withHeader('apikey', $this->apiKey);
        $response = $this->client->sendRequest($request);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (!isset($responseData) || isset($responseData['message'])) {
            throw new CurrencyRatesException('Rates API request failed. Check the provider\'s API and try again.');
        }
        if (!isset($responseData['rates']) || !is_array($responseData['rates'])) {
            throw new CurrencyRatesException('Rates in response are empty or invalid format.');
        }

        return $responseData;
    }

    /**
     * @param array<mixed> $rates
     * @return void
     * @throws InvalidArgumentException
     */
    private function cacheRates(array $rates): void
    {
        $this->cache->set(self::CACHE_KEY, $rates, self::CACHE_TTL);
    }
}
