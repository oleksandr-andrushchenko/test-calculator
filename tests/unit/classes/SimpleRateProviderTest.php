<?php

use Calculator\Model\Transaction;
use Calculator\Provider\DTO\RatesDTO;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Calculator\Provider\SimpleRateProvider;

/**
 * Class SimpleRateProviderTest
 */
class SimpleRateProviderTest extends TestCase
{
    /**
     * @return array
     */
    public function getRateDataProvider(): array
    {
        return [
            [
                new Transaction('EUR', 100.00, 45717360),
                $object = json_decode('{"rates":{"CAD":1.5688},"base":"EUR","date":"2020-08-19"}'),
                [
                    $object,
                    $this->createRatesDTOFromObject($object),
                ],
                null,
            ],
            [
                new Transaction('USD', 50.00, 516793),
                $object = json_decode('{"rates":{"CAD":1.5688,"USD":1.1933},"base":"EUR","date":"2020-08-19"}'),
                [
                    $object,
                    $this->createRatesDTOFromObject($object),
                ],
                1.1933,
            ],
        ];
    }

    /**
     * @dataProvider getRateDataProvider
     * @param Transaction $transaction
     * @param mixed $getRatesDecodedJson
     * @param array $map
     * @param null|float $expectedRate
     */
    public function testGetRate(Transaction $transaction, $getRatesDecodedJson, array $map, ?float $expectedRate)
    {
        /** @var JsonMapper|MockObject $jsonMapper */
        $jsonMapper = $this->createMock(JsonMapper::class);

        /** @var SimpleRateProvider|MockObject $provider */
        $provider = $this->getMockBuilder(SimpleRateProvider::class)
            ->setConstructorArgs([$jsonMapper])
            ->onlyMethods(['getRatesDecodedJson'])
            ->getMock();

        $provider->expects($this->once())
            ->method('getRatesDecodedJson')
            ->willReturn($getRatesDecodedJson);

        [$object, $dto] = $map;

        $jsonMapper->expects($this->once())
            ->method('map')
            ->with($object, new RatesDTO())
            ->willReturn($dto);

        $rate = $provider->getRate($transaction);

        $this->assertEquals($expectedRate, $rate);
    }

    /**
     * @param stdClass $data
     * @return RatesDTO
     */
    private function createRatesDTOFromObject(stdClass $data): RatesDTO
    {
        $dto = new RatesDTO();

        $dto->rates = (array)$data->rates;
        $dto->base = $data->base;
        $dto->date = $data->date;

        return $dto;
    }
}