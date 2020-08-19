<?php

use Calculator\Model\Country;
use Calculator\Model\Transaction;
use Calculator\Provider\DTO\BinDTO;
use Calculator\Provider\DTO\BinDTO\Bank;
use Calculator\Provider\DTO\BinDTO\Number;
use Calculator\Provider\SimpleBinProvider;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class SimpleBinProviderTest
 */
class SimpleBinProviderTest extends TestCase
{
    /**
     * @return array
     */
    public function getCountryDataProvider(): array
    {
        return [
            [
                new Transaction('EUR', 100.00, 45717360),
                [
                    45717360,
                    $object = json_decode('{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Visa/Dankort","prepaid":false,"country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank","url":"www.jyskebank.dk","phone":"+4589893300","city":"Hjørring"}}'),
                ],
                [
                    $object,
                    $this->createBinDTOFromObject($object),
                ],
                new Country('DK'),
            ],
            [
                new Transaction('USD', 50.00, 516793),
                [
                    516793,
                    $object2 = json_decode('{"number":{},"scheme":"mastercard","type":"debit","brand":"Debit","country":{"numeric":"440","alpha2":"LT","name":"Lithuania","emoji":"🇱🇹","currency":"EUR","latitude":56,"longitude":24},"bank":{}}'),
                ],
                [
                    $object2,
                    $this->createBinDTOFromObject($object2),
                ],
                new Country('LT'),
            ],
        ];
    }

    /**
     * @dataProvider getCountryDataProvider
     * @param Transaction $transaction
     * @param array $getBinDecodedJson
     * @param array $map
     * @param Country $expectedCountry
     */
    public function testGetCountry(Transaction $transaction, array $getBinDecodedJson, array $map, Country $expectedCountry)
    {
        /** @var JsonMapper|MockObject $jsonMapper */
        $jsonMapper = $this->createMock(JsonMapper::class);

        /** @var SimpleBinProvider|MockObject $provider */
        $provider = $this->getMockBuilder(SimpleBinProvider::class)
            ->setConstructorArgs([$jsonMapper])
            ->onlyMethods(['getBinDecodedJson'])
            ->getMock();

        [$bin, $data] = $getBinDecodedJson;

        $provider->expects($this->once())
            ->method('getBinDecodedJson')
            ->with($bin)
            ->willReturn($data);

        [$object, $dto] = $map;

        $jsonMapper->expects($this->once())
            ->method('map')
            ->with($object, new BinDTO())
            ->willReturn($dto);

        $country = $provider->getCountry($transaction);

        $this->assertEquals($expectedCountry, $country);
    }

    /**
     * @param mixed $data
     * @return BinDTO
     */
    private function createBinDTOFromObject($data): BinDTO
    {
        $dto = new BinDTO();

        foreach ($data as $k => $v) {
            $dto->$k = $v;
        }

        $dto->number = new Number();

        foreach ($data->number as $k => $v) {
            $dto->number->$k = $v;
        }

        $dto->country = new BinDTO\Country();

        foreach ($data->country as $k => $v) {
            $dto->country->$k = $v;
        }

        $dto->bank = new Bank();

        foreach ($data->bank as $k => $v) {
            $dto->bank->$k = $v;
        }

        return $dto;
    }
}