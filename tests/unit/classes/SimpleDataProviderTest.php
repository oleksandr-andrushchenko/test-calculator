<?php

use Calculator\Provider\DTO\TransactionDTO;
use Calculator\Provider\SimpleDataProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
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
                    [
                        $object1 = json_decode('{"bin":"45717360","amount":"100.00","currency":"EUR"}'),
                        $this->createTransactionDTOFromObject($object1),
                    ],
                    [
                        $object2 = json_decode('{"bin":"516793","amount":"50.00","currency":"USD"}'),
                        $dto2 = $this->createTransactionDTOFromObject($object2),
                    ],
                    [
                        $object3 = json_decode('{"bin":"45417360","amount":"10000.00","currency":"JPY"}'),
                        $dto3 = $this->createTransactionDTOFromObject($object3),
                    ],
                    [
                        $object4 = json_decode('{"bin":"41417360","amount":"130.00","currency":"USD"}'),
                        $dto4 = $this->createTransactionDTOFromObject($object4),
                    ],
                    [
                        $object5 = json_decode('{"bin":"4745030","amount":"2000.00","currency":"GBP"}'),
                        $dto5 = $this->createTransactionDTOFromObject($object5),
                    ],
                ],
                [
                    new Transaction('EUR', 100.00, 45717360),
                    $t2 = new Transaction('USD', 50.00, 516793),
                    $t3 = new Transaction('JPY', 10000.00, 45417360),
                    $t4 = new Transaction('USD', 130.00, 41417360),
                    $t5 = new Transaction('GBP', 2000.00, 4745030),
                ],
            ],
            [
                dirname(__DIR__, 1) . '/fixtures/data_invalid_1st_row.txt',
                [
                    [$object2, $dto2],
                    [$object3, $dto3],
                    [$object4, $dto4],
                    [$object5, $dto5],
                ],
                [
                    $t2,
                    $t3,
                    $t4,
                    $t5,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getTransactionsDataProvider
     * @param string $filename
     * @param array $map
     * @param array $expectedTransactions
     */
    public function testGetTransactions(string $filename, array $map, array $expectedTransactions)
    {
        /** @var JsonMapper|MockObject $jsonMapper */
        $jsonMapper = $this->createMock(JsonMapper::class);

        foreach ($map as $i => $pair) {
            [$object, $dto] = $pair;

            $jsonMapper->expects($this->at($i))
                ->method('map')
                ->with($object, new TransactionDTO())
                ->willReturn($dto);
        }

        $provider = new SimpleDataProvider($filename, $jsonMapper);

        $transactions = $provider->getTransactions();

        $this->assertIsArray($transactions);
        $this->assertEquals($expectedTransactions, $transactions);
    }

    /**
     * @param stdClass $data
     * @return TransactionDTO
     */
    private function createTransactionDTOFromObject(stdClass $data): TransactionDTO
    {
        $dto = new TransactionDTO();

        foreach ($data as $k => $v) {
            $dto->$k = $v;
        }

        return $dto;
    }
}