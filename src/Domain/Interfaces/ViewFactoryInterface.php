<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

interface ViewFactoryInterface
{
    /**
     * @param string $viewPath
     * @return ViewInterface
     */
    public function create(string $viewPath = ''): ViewInterface;
}
