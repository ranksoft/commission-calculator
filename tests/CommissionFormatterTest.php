<?php
declare(strict_types=1);

use Brick\Math\Exception\NumberFormatException;
use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Exception\UnknownCurrencyException;
use PHPUnit\Framework\TestCase;
use CommissionCalculator\Application\Services\CommissionFormatter;
use Brick\Money\Money;

class CommissionFormatterTest extends TestCase
{
    /**
     * @throws UnknownCurrencyException
     * @throws RoundingNecessaryException
     * @throws NumberFormatException
     */
    public function testFormatWithFractionalPart(): void
    {
        $formatter = new CommissionFormatter();

        $money = Money::of('123.456', 'USD', new CustomContext(2), RoundingMode::UP);
        $formatted = $formatter->format($money);

        $this->assertEquals('123.46', $formatted);
    }

    /**
     * @throws UnknownCurrencyException
     * @throws RoundingNecessaryException
     * @throws NumberFormatException
     */
    public function testFormatWithoutFractionalPart(): void
    {
        $formatter = new CommissionFormatter();

        $money = Money::of('123.00', 'USD', new CustomContext(2), RoundingMode::UP);
        $formatted = $formatter->format($money);

        $this->assertEquals('123', $formatted);
    }
}
