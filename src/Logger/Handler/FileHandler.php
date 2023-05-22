<?php
declare(strict_types=1);

namespace CommissionCalculator\App\Logger\Handler;

class FileHandler implements HandlerInterface
{
    const DIRECTORY_PATH = __DIR__ . '/../../../var/';

    private string $logFileDir;

    public function __construct()
    {
        $dir = self::DIRECTORY_PATH;
        if (!\file_exists($dir)) {
            $status = \mkdir($dir, 0777, true);
            if ($status === false && !\is_dir($dir)) {
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s"', $dir));
            }
        }

        $this->logFileDir = $dir;
    }

    public function handle(array $vars): void
    {
        $level = $vars['level'] ?? 'log';
        $output = self::DEFAULT_FORMAT;
        foreach ($vars as $var => $val) {
            $output = \str_replace('%' . $var . '%', $val, $output);
        }
        \file_put_contents($this->logFileDir . $level . '.log', $output . PHP_EOL, FILE_APPEND);
    }
}