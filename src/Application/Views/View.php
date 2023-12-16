<?php
declare(strict_types=1);

namespace CommissionCalculator\Application\Views;

use CommissionCalculator\Domain\Interfaces\ViewInterface;

class View implements ViewInterface
{
    /**
     * @var array<mixed>
     */
    private array $variables = [];

    public function __construct(
        private string $viewPath
    ) {}

    /**
     * @param string $key
     * @param string|int|float|array<mixed> $value
     * @return void
     */
    public function set(string $key, string|int|float|array $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param string $template
     * @return string
     */
    public function render(string $template): string
    {
        extract($this->variables);
        ob_start();
        include($this->viewPath . $template);
        return (string)ob_get_clean();
    }
}
