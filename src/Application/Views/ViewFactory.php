<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Views;

use CommissionCalculator\Domain\Interfaces\ViewFactoryInterface;
use CommissionCalculator\Domain\Interfaces\ViewInterface;

class ViewFactory implements ViewFactoryInterface
{
    /**
     * @var string
     */
    private string $viewBasePath;

    public function __construct(string $viewBasePath = __DIR__ . DIRECTORY_SEPARATOR . 'templates')
    {
        $this->viewBasePath = rtrim($viewBasePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $viewPath
     * @return ViewInterface
     */
    public function create(string $viewPath = ''): ViewInterface
    {
        return new View($this->viewBasePath . ltrim($viewPath, DIRECTORY_SEPARATOR));
    }
}
