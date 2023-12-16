<?php
// config/services.php

use CommissionCalculator\Application\Services\CommissionFormatter;
use CommissionCalculator\Application\Services\Router;
use CommissionCalculator\Application\Views\ViewFactory;
use CommissionCalculator\Domain\Entities\Transaction;
use CommissionCalculator\Domain\Interfaces\BinProviderInterface;
use CommissionCalculator\Domain\Interfaces\CommissionCalculatorInterface;
use CommissionCalculator\Domain\Interfaces\CommissionFormatterInterface;
use CommissionCalculator\Domain\Interfaces\CountryCodeProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryProviderInterface;
use CommissionCalculator\Domain\Interfaces\CountryValidatorInterface;
use CommissionCalculator\Domain\Interfaces\CurrencyRateProviderInterface;
use CommissionCalculator\Domain\Interfaces\CurrencyRatesProviderInterface;
use CommissionCalculator\Domain\Interfaces\RouterInterface;
use CommissionCalculator\Domain\Interfaces\TransactionInterface;
use CommissionCalculator\Domain\Interfaces\ViewFactoryInterface;
use CommissionCalculator\Domain\Repositories\TransactionRepositoryInterface;
use CommissionCalculator\Domain\Services\CommissionCalculator;
use CommissionCalculator\Domain\Services\CountryValidator;
use CommissionCalculator\Infrastructure\Api\BinProvider;
use CommissionCalculator\Infrastructure\Api\CountryCodeProvider;
use CommissionCalculator\Infrastructure\Api\CurrencyRateProvider;
use CommissionCalculator\Infrastructure\Api\CurrencyRatesProvider;
use CommissionCalculator\Infrastructure\Cache\FileCache;
use CommissionCalculator\Infrastructure\Config\Config;
use CommissionCalculator\Infrastructure\DI\DIContainer;
use CommissionCalculator\Infrastructure\Logger\Handler\FileHandler;
use CommissionCalculator\Infrastructure\Logger\Handler\HandlerInterface;
use CommissionCalculator\Infrastructure\Logger\Logger;
use CommissionCalculator\Infrastructure\Persistence\Repositories\FileTransactionRepository;
use CommissionCalculator\Infrastructure\Services\EuCountriesProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

return [
    ContainerInterface::class => DIContainer::class,
    RouterInterface::class => Router::class,
    CommissionFormatterInterface::class => CommissionFormatter::class,
    CountryCodeProviderInterface::class => CountryCodeProvider::class,
    CountryValidatorInterface::class => CountryValidator::class,
    CurrencyRateProviderInterface::class => CurrencyRateProvider::class,
    TransactionInterface::class => Transaction::class,
    HandlerInterface::class => FileHandler::class,
    TransactionRepositoryInterface::class => FileTransactionRepository::class,
    ClientInterface::class => Client::class,
    LoggerInterface::class => Logger::class,
    CacheInterface::class => FileCache::class,
    RequestFactoryInterface::class => HttpFactory::class,
    ViewFactoryInterface::class => ViewFactory::class,
    Config::class => function () {
        return new Config();
    },
    CountryProviderInterface::class => [
        'class' => EuCountriesProvider::class,
        'arguments' => [
            'euCountries' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get('eu_countries');
            }
        ]
    ],
    BinProviderInterface::class => [
        'class' => BinProvider::class,
        'arguments' => [
            'binListUrl' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get(['binlist', 'url']);
            }
        ]
    ],
    CurrencyRatesProviderInterface::class => [
        'class' => CurrencyRatesProvider::class,
        'arguments' => [
            'exchangeUrl' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get(['apilayer', 'exchange_url']);
            },
            'apiKey' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get(['apilayer', 'apikey']);
            }
        ]
    ],
    CommissionCalculatorInterface::class => [
        'class' => CommissionCalculator::class,
        'arguments' => [
            'commissionEu' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get(['commission', 'eu']);
            },
            'commissionDefault' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get(['commission', 'default']);
            },
            'baseCurrency' => function (ContainerInterface $container) {
                return $container->get(Config::class)->get('currency');
            }
        ]
    ],
];
