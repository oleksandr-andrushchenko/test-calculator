<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;
use Calculator\Provider\DTO\TransactionDTO;
use Calculator\Provider\Traits\JsonMapperHolder;
use JsonMapper;

/**
 * Class SimpleDataProvider
 * @package Calculator\Provider
 */
class SimpleDataProvider implements DataProviderInterface
{
    use JsonMapperHolder {
        __construct as jsonMapperConstruct;
    }

    /**
     * @var string
     */
    private $filename;

    /**
     * SimpleDataProvider constructor.
     * @param string $filename
     * @param object|JsonMapper $jsonMapper
     */
    public function __construct(string $filename, JsonMapper $jsonMapper)
    {
        $this->filename = $filename;
        $this->jsonMapperConstruct($jsonMapper);
    }

    /**
     * @inheritDoc
     */
    public function getTransactions(): array
    {
        $transactions = [];

        foreach ($this->getTransactionsJsonRows() as $json) {
            $data = json_decode($json);

            if (!$data) {
                continue;
            }

            /** @var TransactionDTO $dto */
            $dto = $this->jsonMapper->map($data, new TransactionDTO());

            $transactions[] = new Transaction(
                $dto->currency,
                $dto->amount,
                $dto->bin
            );
        }

        return $transactions;
    }

    /**
     * @return array
     */
    private function getTransactionsJsonRows(): array
    {
        $content = file_get_contents($this->filename);
        $rows = explode("\n", $content);
        $rows = array_map('trim', $rows);
        $rows = array_filter($rows);

        return $rows;
    }
}
