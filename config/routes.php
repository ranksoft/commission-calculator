<?php
// routes.php
use CommissionCalculator\Application\Controllers\CommissionController;
use CommissionCalculator\Application\Controllers\HomeController;

return [
    '/' => ['controller' => HomeController::class, 'action' => 'index'],
    '/commission' => ['controller' => CommissionController::class, 'action' => 'index']
];
