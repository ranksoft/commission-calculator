<?php
namespace CommissionCalculator\Application\Controllers;

use CommissionCalculator\Domain\Exceptions\NotFoundException;
use CommissionCalculator\Domain\Interfaces\RouterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class FrontController
{
    public function __construct(
        private readonly ContainerInterface $di,
        private readonly RouterInterface $router
    ) {}

    /**
     * @return void
     * @throws ContainerExceptionInterface
     */
    public function run(): void
    {
        $uri = (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $route = $this->router->getRoute($uri);

        try {
            if (!$route) {
                throw new NotFoundException('Route not found.');
            }

            $controllerName = $route['controller'];
            $actionName = $route['action'];

            if (!class_exists($controllerName) || !method_exists($controllerName, $actionName)) {
                throw new NotFoundException('Controller or action not found.');
            }

            $controller = $this->di->get($controllerName);
            $response = $controller->$actionName();
            echo $response;
        } catch (NotFoundExceptionInterface $e) {
            $this->handleNotFound();
        }
    }

    /**
     * @return void
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        echo "404 Not Found";
        exit();
    }
}
