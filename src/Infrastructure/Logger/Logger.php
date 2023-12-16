<?php
declare(strict_types=1);

namespace CommissionCalculator\Infrastructure\Logger;

use CommissionCalculator\Infrastructure\Logger\Handler\HandlerInterface;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected const DEFAULT_DATETIME_FORMAT = 'c';

    public function __construct(
        private readonly HandlerInterface $handler
    ) {}

    /**
     * Logs with an arbitrary level.
     *
     * @param string $level
     * @param string|\Stringable $message
     * @param array<mixed> $context
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
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     * @param array<mixed> $context
     * @return string
     */
    protected static function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if ((\is_object($val))
                && \method_exists($val, '__toString')) {
                $replace['{' . $key . '}'] = $val;
            }

        }
        return \strtr($message, $replace);
    }
}