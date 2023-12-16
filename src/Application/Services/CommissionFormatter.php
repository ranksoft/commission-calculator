<?php
declare(strict_types=1);
namespace CommissionCalculator\Application\Services;

use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Money;
use CommissionCalculator\Domain\Interfaces\CommissionFormatterInterface;

class CommissionFormatter implements CommissionFormatterInterface
{
    /**
     * @inheritdoc
     * @throws RoundingNecessaryException
     */
    public function format(Money $commission): string
    {
        $fractionalPart = $commission->getAmount()->getFractionalPart();
        if (!(int)$fractionalPart) {
            return $commission->getAmount()->getIntegralPart();
        }

        return (string)$commission->getAmount()->toScale(2, RoundingMode::UP);
    }
}
