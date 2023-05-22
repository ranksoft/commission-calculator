<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Service;

use CommissionCalculator\App\Exception\ConfigException;
use CommissionCalculator\App\Interface\BinProviderInterface;
use CommissionCalculator\App\Model\Config;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;

class BinProvider implements BinProviderInterface
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
     * @param string $bin
     * @return array<mixed>
     * @throws ClientExceptionInterface
     * @throws ConfigException
     */
    public function lookup(string $bin): array
    {
        try {
            $binListUrl = $this->config->getBinListUrl();
            $request = $this->requestFactory->createRequest('GET', $binListUrl . $bin);
            $response = $this->client->sendRequest($request);
            return \json_decode((string)$response->getBody(), true);
        } catch (ClientExceptionInterface|ConfigException $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }
}