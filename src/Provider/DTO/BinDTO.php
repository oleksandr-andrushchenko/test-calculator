<?php

namespace Calculator\Provider\DTO;

/**
 * Class BinDTO
 * @package Calculator\Provider\DTO
 */
class BinDTO
{
    /**
     * @var \Calculator\Provider\DTO\BinDTO\Number
     */
    public $number;

    /**
     * @var string
     */
    public $scheme;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $brand;

    /**
     * @var null|bool
     */
    public $prepaid;

    /**
     * @var \Calculator\Provider\DTO\BinDTO\Country
     */
    public $country;

    /**
     * @var \Calculator\Provider\DTO\BinDTO\Bank
     */
    public $bank;
}