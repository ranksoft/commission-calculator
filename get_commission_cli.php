<?php
declare(strict_types=1);

use CommissionCalculator\Application\Services\TransactionProcessor;
use CommissionCalculator\Domain\Repositories\TransactionRepositoryInterface;
use CommissionCalculator\Infrastructure\DI\DIContainer;

require_once 'vendor/autoload.php';

$di = new DIContainer();
$transactionRepository = null;
if(isset($argv[1])) {
    $transactionRepository = $di->get(TransactionRepositoryInterface::class, ['transactionsFilename' => $argv[1]]);
}
if(!isset($argv[1])) {
    $transactionRepository = $di->get(TransactionRepositoryInterface::class);
}

$transactionProcessor = $di->get(TransactionProcessor::class);
$transactions = $transactionRepository->getTransactions();
$commissions = $transactionProcessor->processTransactions($transactions);
foreach ($commissions as $commission) {
    echo $commission;
    print "\n";
}