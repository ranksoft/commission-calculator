<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Interface;

interface BinProviderInterface
{
    /**
     * @param string $bin
     * @return array<mixed>
     */
    public function lookup(string $bin): array;
}