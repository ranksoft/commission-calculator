<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Controllers;

use CommissionCalculator\Application\Services\TransactionProcessor;
use CommissionCalculator\Domain\Interfaces\ViewFactoryInterface;
use CommissionCalculator\Domain\Repositories\TransactionRepositoryInterface;

class CommissionController
{
    public function __construct
    (
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly TransactionProcessor $transactionProcessor,
        private readonly ViewFactoryInterface $viewFactory
    ) {}

    /**
     * @return string
     */
    public function index(): string
    {
        $transactions = $this->transactionRepository->getTransactions();
        $commissions = $this->transactionProcessor->processTransactions($transactions);
        $view = $this->viewFactory->create();
        $view->set('commissions', $commissions);
        return $view->render('commission/view.php');
    }
}
