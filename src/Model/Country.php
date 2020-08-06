<?php

namespace Calculator\Model;

/**
 * Class Transaction
 * @package Calculator\Model
 */
class Country
{
    /**
     * @var string
     */
    private $alpha2;

    /**
     * Country constructor.
     * @param string $alpha2
     */
    public function __construct(string $alpha2)
    {
        $this->alpha2 = $alpha2;
    }

    /**
     * @inheritDoc
     */
    public function getAlpha2(): string
    {
        return $this->alpha2;
    }

    /**
     * @return bool
     */
    public function isEu(): bool
    {
        switch ($this->getAlpha2()) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                return true;
            default:
                return false;
        }
    }
}
