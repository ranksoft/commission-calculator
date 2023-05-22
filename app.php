<?php

use CommissionCalculator\App\Logger\Handler\FileHandler;
use CommissionCalculator\App\Logger\Logger;
use CommissionCalculator\App\Model\Config;
use CommissionCalculator\App\Repository\FileTransactionRepository;
use CommissionCalculator\App\Service\BinProvider;
use CommissionCalculator\App\Service\CommissionCalculator;
use CommissionCalculator\App\Service\CountryCodeProvider;
use CommissionCalculator\App\Service\CountryValidator;
use CommissionCalculator\App\Service\CurrencyRateProvider;
use CommissionCalculator\App\Service\CurrencyRatesProvider;
use CommissionCalculator\App\Service\EuCountriesProvider;
use CommissionCalculator\App\Service\TransactionProcessor;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

require_once 'vendor/autoload.php';

$handler = new FileHandler();
$fileLogger = new Logger($handler);
$config = new Config();
$client = new Client();
$requestFactory = new HttpFactory();
$euCountriesProvider = new EuCountriesProvider($config, $fileLogger);
$binProvider = new BinProvider($config, $client, $requestFactory, $fileLogger);
$countryCodeProvider = new CountryCodeProvider($binProvider);
$currencyRatesProvider = new CurrencyRatesProvider($config, $client, $requestFactory, $fileLogger);
$currencyRateProvider = new CurrencyRateProvider($currencyRatesProvider);
$countryValidator = new CountryValidator($euCountriesProvider);
$commissionCalculator = new CommissionCalculator($countryValidator, $currencyRateProvider, $countryCodeProvider, $config, $fileLogger);
//TransactionProcessor is the class for work demonstrate transaction processor.
$transactionProcessor = new TransactionProcessor($commissionCalculator);
if(isset($argv[1])) {
    $fileTransactionRepository = new FileTransactionRepository($fileLogger, $argv[1]);
}
if(!isset($argv[1])) {
    $fileTransactionRepository = new FileTransactionRepository($fileLogger);
}

$transactions = $fileTransactionRepository->getTransactions();
$сommissions = $transactionProcessor->сommissionСalculation($transactions);
foreach ($сommissions as $сommission) {
    echo $сommission;
    print "\n";
}