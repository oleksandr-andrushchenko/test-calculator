<?php

use Calculator\Provider\SimpleDataProvider;
use PHPUnit\Framework\TestCase;

use Calculator\Model\Transaction;

/**
 * Class SimpleDataProviderTest
 */
class SimpleDataProviderTest extends TestCase
{
    /**
     * @return array
     */
    public function getTransactionsDataProvider(): array
    {
        return [
            [
                dirname(__DIR__, 1) . '/fixtures/data_ok.txt',
                [
                    new Transaction('EUR', 100.00, 45717360),
                    new Transaction('USD', 50.00, 516793),
                    new Transaction('JPY', 10000.00, 45417360),
                    new Transaction('USD', 130.00, 41417360),
                    new Transaction('GBP', 2000.00, 4745030),
                ],
            ],
            [
                dirname(__DIR__, 1) . '/fixtures/data_invalid_1st_row.txt',
                [
                    new Transaction('USD', 50.00, 516793),
                    new Transaction('JPY', 10000.00, 45417360),
                    new Transaction('USD', 130.00, 41417360),
                    new Transaction('GBP', 2000.00, 4745030),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getTransactionsDataProvider
     * @param string $filename
     * @param array $expectedTransactions
     */
    public function testGetTransactions(string $filename, array $expectedTransactions)
    {
        $jsonMapper = new JsonMapper();
        $provider = new SimpleDataProvider($filename, $jsonMapper);

        $transactions = $provider->getTransactions();

        $this->assertIsArray($transactions);
        $this->assertEquals($expectedTransactions, $transactions);
    }
}