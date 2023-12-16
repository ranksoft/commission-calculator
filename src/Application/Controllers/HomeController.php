<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Controllers;

use CommissionCalculator\Domain\Interfaces\ViewFactoryInterface;

class HomeController
{
    public function __construct(
        private readonly ViewFactoryInterface $viewFactory
    ) {}

    /**
     * @return string
     */
    public function index(): string
    {
        return $this->viewFactory->create()->render('home/view.php');
    }
}
