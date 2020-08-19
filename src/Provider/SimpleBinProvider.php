<?php

namespace Calculator\Provider;

use Calculator\Model\Country;
use Calculator\Model\Transaction;
use Calculator\Provider\DTO\BinDTO;
use Calculator\Provider\Exception\InvalidBinDataException;
use JsonMapper;

/**
 * Class SimpleBinProvider
 * @package Calculator\Provider
 */
class SimpleBinProvider implements BinProviderInterface
{
    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * SimpleBinProvider constructor.
     * @param object|JsonMapper $jsonMapper
     */
    public function __construct(JsonMapper $jsonMapper)
    {
        $this->jsonMapper = $jsonMapper;
    }

    private const SOURCE_ENDPOINT = 'https://lookup.binlist.net/%s';

    /**
     * @inheritDoc
     */
    public function getCountry(Transaction $transaction): Country
    {
        $data = $this->getBinDecodedJson($transaction->getBin());

        /** @var BinDTO $dto */
        $dto = $this->jsonMapper->map($data, new BinDTO());

        return new Country(
            $dto->country->alpha2
        );
    }

    /**
     * @param int $bin
     * @return mixed
     * @throws InvalidBinDataException
     */
    protected function getBinDecodedJson(int $bin)
    {
        $endpoint = sprintf(self::SOURCE_ENDPOINT, $bin);

        $json = file_get_contents($endpoint);

        if (!$json) {
            throw new InvalidBinDataException('Ooops! Invalid Bin Data');
        }

        return json_decode($json);
    }
}