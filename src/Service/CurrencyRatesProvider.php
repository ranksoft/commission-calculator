<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Exception\CurrencyRateException;
use CommissionCalculator\App\Interface\CurrencyRatesProviderInterface;
use CommissionCalculator\App\Model\Config;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;

class CurrencyRatesProvider implements CurrencyRatesProviderInterface
{
    private Config $config;

    private ClientInterface $client;

    private RequestFactoryInterface $requestFactory;

    private LoggerInterface $logger;

    public function __construct(
        Config                  $config,
        ClientInterface         $client,
        RequestFactoryInterface $requestFactory,
        LoggerInterface         $logger
    )
    {
        $this->config = $config;
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
    }

    /**
     * @return array<string>
     * @throws ClientExceptionInterface
     * @throws ConfigException
     * @throws CurrencyRateException
     */
    public function getRates(): array
    {
        try {
            $exchangeUrl = $this->config->getApiLayerExchangeUrl();
            $apiKey = $this->config->getApiLayerKey();
            $request = $this->requestFactory->createRequest('GET', $exchangeUrl);
            $request = $request->withHeader('apikey', $apiKey);
            $response = $this->client->sendRequest($request);
            $response = json_decode((string)$response->getBody(), true);
            if (!isset($response) || isset($response['message'])) {
                throw new CurrencyRateException('Rates api request failed. Check the provider\'s API and try again.');
            }
            if (!isset($response['rates']) && is_array($response['rates'])) {
                throw new CurrencyRateException('Rates in response are empty');
            }

            return $response['rates'];
        } catch (ClientExceptionInterface|ConfigException $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }
}
