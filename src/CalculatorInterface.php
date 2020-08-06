<?php

namespace Calculator;

use Calculator\Provider\Exception\InvalidBinDataException;
use Calculator\Provider\Exception\InvalidRateDataException;

/**
 * Interface CalculatorInterface
 * @package Calculator
 */
interface CalculatorInterface
{
    /**
     * @return float[]
     */
    public function getCommissions(): array;
}