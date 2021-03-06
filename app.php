<?php

require_once 'vendor/autoload.php';

use Calculator\CalculatorInterface;
use Calculator\Container\ContainerInterface;
use Calculator\Container\SimpleContainer;
use Calculator\Provider\DataProviderInterface;
use Calculator\Provider\Decorator\CeilingCommissionsCalculatorDecorator;
use Calculator\Provider\SimpleDataProvider;
use Calculator\Provider\BinProviderInterface;
use Calculator\Provider\SimpleBinProvider;
use Calculator\Provider\RateProviderInterface;
use Calculator\Provider\SimpleRateProvider;
use Calculator\SimpleCalculator;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

if (empty($argv[1])) {
    die('Destination argument required');
}

$container = new SimpleContainer([
    JsonMapper::class => function () {
        $jsonMapper = new JsonMapper();
        $jsonMapper->bExceptionOnMissingData = true;
        $jsonMapper->bStrictObjectTypeChecking = true;
        return $jsonMapper;
    },
    LoggerInterface::class => function () {
        $logger = new Logger('app');

        $handler = new StreamHandler('php://stdout');

        $formatter = new LineFormatter("%message%" . PHP_EOL);
        $formatter->allowInlineLineBreaks();
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        return $logger;
    },
    DataProviderInterface::class => function (ContainerInterface $container, string $filename) {
        return new SimpleDataProvider($filename, $container->get(JsonMapper::class));
    },
    BinProviderInterface::class => function (ContainerInterface $container) {
        return new SimpleBinProvider($container->get(JsonMapper::class));
    },
    RateProviderInterface::class => function (ContainerInterface $container) {
        return new SimpleRateProvider($container->get(JsonMapper::class));
    },
    CalculatorInterface::class => function (ContainerInterface $container, string $filename, string $currency) {
        $calculator = new SimpleCalculator(
            $container->get(DataProviderInterface::class, $filename),
            $container->get(BinProviderInterface::class),
            $container->get(RateProviderInterface::class),
            $container->get(LoggerInterface::class),
            $currency
        );

//        $calculator = new CeilingCommissionsCalculatorDecorator($calculator);

        return $calculator;
    }
]);

/**
 * @var LoggerInterface $logger
 * @var CalculatorInterface $calculator
 */

$logger = $container->get(LoggerInterface::class);

$logger->info('Commissions:');
$calculator = $container->get(CalculatorInterface::class, $argv[1], $argv[2] ?? 'EUR');
$logger->info(implode(PHP_EOL, $calculator->getCommissions()));

$logger->info('Ceiling Commissions:');
$calculator = new CeilingCommissionsCalculatorDecorator($calculator);
$logger->info(implode(PHP_EOL, $calculator->getCommissions()));