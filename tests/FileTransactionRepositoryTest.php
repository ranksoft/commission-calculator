<?php

declare(strict_types=1);

namespace CommissionCalculator\Tests;

use CommissionCalculator\App\Repository\FileTransactionRepository;
use CommissionCalculator\App\Model\Transaction;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class FileTransactionRepositoryTest extends TestCase
{
    /**
     * @var false|resource
     */
    private $tempFile;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->tempFile = tmpfile();
        $data = [
            '{"bin":"45717360","amount":"100.00","currency":"EUR"}',
            '{"bin":"516793","amount":"50.00","currency":"USD"}',
            '{"bin":"45417360","amount":"10000.00","currency":"JPY"}',
            '{"bin":"41417360","amount":"130.00","currency":"USD"}',
            '{"bin":"4745030","amount":"2000.00","currency":"GBP"}'
        ];
        if (is_resource($this->tempFile)) {
            fwrite($this->tempFile, implode("\n", $data));
        }
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        if (is_resource($this->tempFile)) {
            fclose($this->tempFile);
        }
    }

    /**
     * @return void
     */
    public function testGetTransactions(): void
    {
        if (!is_resource($this->tempFile)) {
            $this->fail('Temporary file not exist!');
        }

        $path = stream_get_meta_data($this->tempFile)['uri'];
        $logger = new NullLogger();
        $repository = new FileTransactionRepository($logger, $path);
        $transactions = $repository->getTransactions();
        $this->assertCount(5, $transactions);
        $this->assertInstanceOf(Transaction::class, $transactions[2]);
        $this->assertEquals('45417360', $transactions[2]->getBin());
        $this->assertEquals(10000.00, $transactions[2]->getAmount());
        $this->assertEquals('JPY', $transactions[2]->getCurrency());
    }
}
