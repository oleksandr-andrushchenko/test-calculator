<?php

namespace Calculator\Provider;

use Calculator\Model\Country;
use Calculator\Provider\DTO\BinDTO;
use Calculator\Provider\Exception\InvalidBinDataException;

use Calculator\Provider\Traits\JsonMapperHolder;

/**
 * Class SimpleBinProvider
 * @package Calculator\Provider
 */
class SimpleBinProvider implements BinProviderInterface
{
    use JsonMapperHolder;

    private const SOURCE_ENDPOINT = 'https://lookup.binlist.net/%s';

    /**
     * @inheritDoc
     */
    public function getCountry(int $bin): Country
    {
        $endpoint = sprintf(self::SOURCE_ENDPOINT, $bin);

        $json = file_get_contents($endpoint);

        if (!$json) {
            throw new InvalidBinDataException('Ooops! Invalid Bin Data');
        }

        $data = json_decode($json);

        /** @var BinDTO $dto */
        $dto = $this->jsonMapper->map($data, new BinDTO());

        return new Country(
            $dto->country->alpha2
        );
    }
}