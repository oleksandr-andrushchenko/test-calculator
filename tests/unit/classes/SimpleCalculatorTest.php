<?php

use Calculator\Model\Country;
use Calculator\SimpleCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Calculator\Provider\SimpleBinProvider;
use Calculator\Provider\SimpleRateProvider;
use Calculator\Provider\SimpleDataProvider;
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
                    new Transaction('EUR', 100.00, 45717360),
                    new Transaction('USD', 50.00, 516793),
                    new Transaction('JPY', 10000.00, 45417360),
                    new Transaction('USD', 130.00, 41417360),
                    new Transaction('GBP', 2000.00, 4745030),
                ],
                [
                    45717360 => new Country('DK'),
                    516793 => new Country('LT'),
                    45417360 => new Country('JP'),
                    41417360 => new Country('US'),
                    4745030 => new Country('GB'),
                ],
                [
                    ['EUR', 0.00],
                    ['USD', 1.1854],
                    ['JPY', 125.37],
                    ['USD', 1.1854],
                    ['GBP', 0.90265]
                ],
                [
                    1,
                    0.42179854901299,
                    1.5952779771875,
                    2.1933524548676,
                    44.313964438044
                ]
            ]
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
        /** @var MockObject|SimpleDataProvider $dataProvider */
        $dataProvider = $this->createMock(SimpleDataProvider::class);

        $dataProvider->method('getTransactions')->willReturn($getTransactions);

        /** @var MockObject|SimpleBinProvider $binProvider */
        $binProvider = $this->createMock(SimpleBinProvider::class);

        $i = 0;
        foreach ($getCountry as $bin => $country) {
            $binProvider->expects($this->at($i++))
                ->method('getCountry')
                ->with($bin)
                ->willReturn($country);
        }

        /** @var MockObject|SimpleRateProvider $rateProvider */
        $rateProvider = $this->createMock(SimpleRateProvider::class);

        $i = 0;
        foreach ($getRate as $pair) {
            list($currency, $rate) = $pair;

            $rateProvider->expects($this->at($i++))
                ->method('getRate')
                ->with($currency)
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
