<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;
use Calculator\Provider\DTO\RatesDTO;
use Calculator\Provider\Exception\InvalidRateDataException;
use Calculator\Provider\Traits\JsonMapperHolder;

/**
 * Class SimpleRateProvider
 * @package Calculator\Provider
 */
class SimpleRateProvider implements RateProviderInterface
{
    use JsonMapperHolder;

    private const SOURCE_ENDPOINT = 'https://api.exchangeratesapi.io/latest';

    /**
     * @inheritDoc
     */
    public function getRate(Transaction $transaction): ?float
    {
        $endpoint = sprintf(self::SOURCE_ENDPOINT);

        $json = file_get_contents($endpoint);

        if (!$json) {
            throw new InvalidRateDataException('Ooops! Invalid Rate Data');
        }

        $data = json_decode($json);

        /** @var RatesDTO $dto */
        $dto = $this->jsonMapper->map($data, new RatesDTO());

        return $dto->rates[$transaction->getCurrency()] ?? null;
    }
}
