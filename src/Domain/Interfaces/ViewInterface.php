<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

interface ViewInterface
{
    /**
     * @param string $key
     * @param string|int|float|array<mixed> $value
     * @return void
     */
    public function set(string $key, string|int|float|array $value): void;

    /**
     * @param string $template
     * @return string
     */
    public function render(string $template): string;
}
