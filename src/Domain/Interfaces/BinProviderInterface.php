<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

use CommissionCalculator\Domain\ValueObjects\Bin;
use CommissionCalculator\Domain\Exceptions\BinProviderException;

interface BinProviderInterface
{
    /**
     * Retrieves data for a given BIN.
     *
     * @param Bin $bin
     * @return array<mixed>
     * @throws BinProviderException
     */
    public function getBinData(Bin $bin): array;
}
