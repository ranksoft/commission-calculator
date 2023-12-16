<?php
declare(strict_types=1);
namespace CommissionCalculator\Domain\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
