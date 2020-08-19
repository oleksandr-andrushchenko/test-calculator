<?php

use Calculator\Model\Country;
use Calculator\Provider\BinProviderInterface;
use Calculator\Provider\DataProviderInterface;
use Calculator\Provider\RateProviderInterface;
use Calculator\SimpleCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Calculator\Model\Transaction;
use Psr\Log\NullLogger;

/**
 * Class SimpleCalculatorTest
 */
class SimpleCalculatorTest extends TestCase
{
    /**
     * @return array
     */
    public function getCommissionsDataProvider(): array
    {
        return [
            [
                [
                    $t1 = new Transaction('EUR', 100.00, 45717360),
                    $t2 = new Transaction('USD', 50.00, 516793),
                    $t3 = new Transaction('JPY', 10000.00, 45417360),
                    $t4 = new Transaction('USD', 130.00, 41417360),
                    $t5 = new Transaction('GBP', 2000.00, 4745030),
                ],
                [
                    [$t1, new Country('DK')],
                    [$t2, new Country('LT')],
                    [$t3, new Country('JP')],
                    [$t4, new Country('US')],
                    [$t5, new Country('GB')],
                ],
                [
                    [$t1, 1.00],
                    [$t2, 1.1854],
                    [$t3, 125.37],
                    [$t4, 1.1854],
                    [$t5, 0.90265],
                ],
                [
                    1,
                    0.42179854901299,
                    1.5952779771875,
                    2.1933524548676,
                    44.313964438044,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getCommissionsDataProvider
     * @param array $getTransactions
     * @param array $getCountry
     * @param array $getRate
     * @param array $expectedCommissions
     */
    public function testGetCommissions(
        array $getTransactions,
        array $getCountry,
        array $getRate,
        array $expectedCommissions
    ) {
        /** @var MockObject|DataProviderInterface $dataProvider */
        $dataProvider = $this->createMock(DataProviderInterface::class);

        $dataProvider->method('getTransactions')->willReturn($getTransactions);

        /** @var MockObject|BinProviderInterface $binProvider */
        $binProvider = $this->createMock(BinProviderInterface::class);

        foreach ($getCountry as $i => $pair) {
            [$transaction, $country] = $pair;

            $binProvider->expects($this->at($i))
                ->method('getCountry')
                ->with($transaction)
                ->willReturn($country);
        }

        /** @var MockObject|RateProviderInterface $rateProvider */
        $rateProvider = $this->createMock(RateProviderInterface::class);

        foreach ($getRate as $i => $pair) {
            [$transaction, $rate] = $pair;

            $rateProvider->expects($this->at($i))
                ->method('getRate')
                ->with($transaction)
                ->willReturn($rate);
        }

        $logger = new NullLogger();

        $calculator = new SimpleCalculator(
            $dataProvider,
            $binProvider,
            $rateProvider,
            $logger
        );

        $commissions = $calculator->getCommissions();

        $this->assertIsArray($commissions);
        $this->assertEquals($expectedCommissions, $commissions);
    }
}
