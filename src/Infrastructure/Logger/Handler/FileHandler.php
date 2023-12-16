<?php
declare(strict_types=1);
namespace CommissionCalculator\Infrastructure\Logger\Handler;

class FileHandler implements HandlerInterface
{
    private const DIRECTORY_PATH = __DIR__ . '/../../../../var/log/';
    private const DEFAULT_LOG_DIRECTORY = 'log';
    private const DEFAULT_LOG_LEVEL = 'log';
    private const FILE_PERMISSIONS = 0770;

    /**
     * @var string
     */
    private string $logFileDir;

    public function __construct(string $logFileDir = self::DEFAULT_LOG_DIRECTORY)
    {
        $this->logFileDir = $logFileDir === self::DEFAULT_LOG_DIRECTORY
            ? self::DIRECTORY_PATH
            : $logFileDir;
        $this->ensureDirectoryExists();
    }

    /**
     * @return void
     */
    private function ensureDirectoryExists(): void
    {
        if (!\file_exists($this->logFileDir)) {
            $this->createDirectory();
        }
    }

    /**
     * @return void
     */
    private function createDirectory(): void
    {
        $status = \mkdir($this->logFileDir, self::FILE_PERMISSIONS, true);
        if ($status === false && !\is_dir($this->logFileDir)) {
            throw new \UnexpectedValueException(sprintf('Cannot create directory at "%s"', $this->logFileDir));
        }
    }

    /**
     * @param array<mixed> $vars
     * @return void
     */
    public function handle(array $vars): void
    {
        $level = $vars['level'] ?? self::DEFAULT_LOG_LEVEL;
        $output = $this->formatLog($vars);
        \file_put_contents($this->logFileDir . $level . '.log', $output . PHP_EOL, FILE_APPEND);
    }

    /**
     * @param array<mixed> $vars
     * @return string
     */
    private function formatLog(array $vars): string
    {
        $output = self::DEFAULT_FORMAT;
        foreach ($vars as $var => $val) {
            $output = \str_replace('%' . $var . '%', $val, $output);
        }
        return $output;
    }
}
