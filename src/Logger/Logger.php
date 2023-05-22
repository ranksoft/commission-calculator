<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Logger;

use CommissionCalculator\App\Logger\Handler\HandlerInterface;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected const DEFAULT_DATETIME_FORMAT = 'c';

    private HandlerInterface $handler;

    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param string $level
     * @param string|\Stringable $message
     * @param string[] $context
     * @return void
     */
    public function log($level, string|\Stringable $message, array $context = array()): void
    {
        $level = (string)$level;
        $this->handler->handle([
            'message' => self::interpolate((string)$message, $context),
            'level' => \strtoupper($level),
            'timestamp' => (new \DateTimeImmutable())->format(self::DEFAULT_DATETIME_FORMAT),
        ]);
    }

    /**
     * @param string $message
     * @param string[] $context
     * @return string
     */
    protected static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (\method_exists($val, '__toString')) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return \strtr($message, $replace);
    }
}