<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;
use Calculator\Provider\DTO\TransactionDTO;
use JsonMapper;

/**
 * Class SimpleDataProvider
 * @package Calculator\Provider
 */
class SimpleDataProvider implements DataProviderInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * SimpleDataProvider constructor.
     * @param string $filename
     * @param object|JsonMapper $jsonMapper
     */
    public function __construct(string $filename, JsonMapper $jsonMapper)
    {
        $this->filename = $filename;
        $this->jsonMapper = $jsonMapper;
    }

    /**
     * @inheritDoc
     */
    public function getTransactions(): array
    {
        $transactions = [];

        foreach ($this->getTransactionsDecodedJsonRows() as $data) {
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
     * @return mixed[]
     */
    private function getTransactionsDecodedJsonRows(): array
    {
        $content = file_get_contents($this->filename);
        $rows = explode("\n", $content);
        $rows = array_map('trim', $rows);
        $rows = array_filter($rows);

        return array_map(function ($json) {
            return json_decode($json);
        }, $rows);
    }
}
