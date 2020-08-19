<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;
use Calculator\Provider\DTO\RatesDTO;
use Calculator\Provider\Exception\InvalidRateDataException;
use JsonMapper;

/**
 * Class SimpleRateProvider
 * @package Calculator\Provider
 */
class SimpleRateProvider implements RateProviderInterface
{
    private const SOURCE_ENDPOINT = 'https://api.exchangeratesapi.io/latest';

    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * SimpleRateProvider constructor.
     * @param object|JsonMapper $jsonMapper
     */
    public function __construct(JsonMapper $jsonMapper)
    {
        $this->jsonMapper = $jsonMapper;
    }

    /**
     * @inheritDoc
     */
    public function getRate(Transaction $transaction): ?float
    {
        $data = $this->getRatesDecodedJson();

        /** @var RatesDTO $dto */
        $dto = $this->jsonMapper->map($data, new RatesDTO());

        return $dto->rates[$transaction->getCurrency()] ?? null;
    }

    /**
     * @return mixed
     * @throws InvalidRateDataException
     */
    protected function getRatesDecodedJson()
    {
        $endpoint = sprintf(self::SOURCE_ENDPOINT);

        $json = file_get_contents($endpoint);

        if (!$json) {
            throw new InvalidRateDataException('Ooops! Invalid Rate Data');
        }

        return json_decode($json);
    }
}
