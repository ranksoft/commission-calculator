<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Interfaces;

interface RouterInterface {

    /**
     * @param string $uri
     * @return array<mixed>|null
     */
    public function getRoute(string $uri): ?array;
}
