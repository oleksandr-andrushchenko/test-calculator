<?php

namespace Calculator\Provider\Decorator;

use Calculator\CalculatorInterface;

/**
 * Class CeilingCommissionsCalculatorDecorator
 * @package Calculator\Provider\Decorator
 */
class CeilingCommissionsCalculatorDecorator implements CalculatorInterface
{
    public const DEFAULT_PRECISION = 2;

    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var int
     */
    private $precision;

    /**
     * CeilingCommissionsCalculatorDecorator constructor.
     * @param CalculatorInterface $calculator
     * @param int $precision
     */
    public function __construct(CalculatorInterface $calculator, int $precision = self::DEFAULT_PRECISION)
    {
        $this->calculator = $calculator;
        $this->precision = $precision;
    }

    /**
     * @inheritDoc
     */
    public function getCommissions(): array
    {
        return array_map(function (float $commission) {
            $coefficient = pow(10, $this->precision);

            return ceil($commission * $coefficient) / $coefficient;
        }, $this->calculator->getCommissions());
    }
}
