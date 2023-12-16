<?php
declare(strict_types=1);

use CommissionCalculator\Application\Controllers\FrontController;
use CommissionCalculator\Infrastructure\DI\DIContainer;

require_once '../vendor/autoload.php';

$di = new DIContainer();

/**
 * @var FrontController $frontController
 */
$frontController = $di->get(FrontController::class);
$frontController->run();
