<?php
namespace Mezon\Service;

/**
 * Base class for all transports
 *
 * @package Service
 * @subpackage ServiceTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class for all transports
 *
 * @author Dodonov A.A.
 */
abstract class ServiceTransport implements \Mezon\Service\ServiceTransportInterface
{

    /**
     * Request params fetcher
     *
     * @var \Mezon\Service\ServiceRequestParamsInterface
     * // TODO make it private
     */
    public $paramsFetcher = false;

    /**
     * Service's logic
     *
     * @var \Mezon\Service\ServiceLogic
     * // TODO make it private
     */
    public $serviceLogic = false;

    /**
     * Router
     *
     * @var \Mezon\Router\Router
     * // TODO make it private
     */
    protected $router = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->router = new \Mezon\Router\Router();
    }

    /**
     * Method searches necessary logic object
     *
     * @param string $method
     *            Necessary method
     * @return \Mezon\Service\ServiceBaseLogicInterface Logic object
     */
    protected function getNecessaryLogic(string $method): \Mezon\Service\ServiceBaseLogicInterface
    {
        if (is_object($this->serviceLogic)) {
            if (method_exists($this->serviceLogic, $method)) {
                return $this->serviceLogic;
            } else {
                throw (new \Exception(
                    'The method "' . $method . '" was not found in the "' . get_class($this->serviceLogic) . '"',
                    - 1));
            }
        } elseif (is_array($this->serviceLogic)) {
            foreach ($this->serviceLogic as $logic) {
                if (method_exists($logic, $method)) {
                    return $logic;
                }
            }

            throw (new \Exception('The method "' . $method . '" was not found in the set of logic objects', - 1));
        } else {
            throw (new \Exception('Logic was not found', - 2));
        }
        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    /**
     * Method creates session
     *
     * @param bool|string $token
     *            Session token
     */
    public function createSession(string $token): string
    {
        // must be overriden
        // TODO mark this method as abstract
        return $token;
    }

    /**
     * Method adds's route
     *
     * @param string $route
     *            Route
     * @param string $method
     *            Logic method to be called
     * @param string|array $requestMethod
     *            HTTP request method
     * @param string $callType
     *            Type of the call
     */
    public function addRoute(string $route, string $method, $requestMethod, string $callType = 'callLogic'): void
    {
        $localServiceLogic = $this->getNecessaryLogic($method);

        if ($callType == 'public_call') {
            $this->router->addRoute(
                $route,
                function () use ($localServiceLogic, $method) {
                    return $this->callPublicLogic($localServiceLogic, $method, []);
                },
                $requestMethod);
        } else {
            $this->router->addRoute(
                $route,
                function () use ($localServiceLogic, $method) {
                    return $this->callLogic($localServiceLogic, $method, []);
                },
                $requestMethod);
        }
    }

    /**
     * Method loads single route
     *
     * @param array $route
     *            Route description
     */
    public function loadRoute(array $route): void
    {
        if (! isset($route['route'])) {
            throw (new \Exception('Field "route" must be set'));
        }
        if (! isset($route['callback'])) {
            throw (new \Exception('Field "callback" must be set'));
        }
        $method = isset($route['method']) ? $route['method'] : 'GET';
        $callType = isset($route['call_type']) ? $route['call_type'] : 'callLogic';

        $this->addRoute($route['route'], $route['callback'], $method, $callType);
    }

    /**
     * Method loads routes
     *
     * @param array $routes
     *            Route descriptions
     */
    public function loadRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            $this->loadRoute($route);
        }
    }

    /**
     * Method loads routes from config file
     *
     * @param string $path
     *            Path to the routes description
     */
    public function loadRoutesFromConfig(string $path = './conf/routes.php')
    {
        if (file_exists($path)) {
            $routes = (include ($path));

            $this->loadRoutes($routes);
        } else {
            throw (new \Exception('Route ' . $path . ' was not found', 1));
        }
    }

    /**
     * Method runs logic functions
     *
     * @param \Mezon\Service\ServiceLogic $serviceLogic
     *            object with all service logic
     * @param string $method
     *            Logic's method to be executed
     * @param array $params
     *            Logic's parameters
     * @return mixed Result of the called method
     */
    public function callLogic(\Mezon\Service\ServiceBaseLogicInterface $serviceLogic, string $method, array $params = [])
    {
        try {
            $params['SessionId'] = $this->createSession($this->getParamsFetcher()->getParam('session_id'));

            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Method runs logic functions
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $serviceLogic
     *            object with all service logic
     * @param string $method
     *            Logic's method to be executed
     * @param array $params
     *            Logic's parameters
     * @return mixed Result of the called method
     */
    public function callPublicLogic(
        \Mezon\Service\ServiceBaseLogicInterface $serviceLogic,
        string $method,
        array $params = [])
    {
        try {
            return call_user_func_array([
                $serviceLogic,
                $method
            ], $params);
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            Exception object
     * @return array Error data
     */
    public function errorResponse($e): array
    {
        return [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => 'service',
            'call_stack' => $this->formatCallStack($e),
            'host' => 'console'
        ];
    }

    /**
     * Method returns parameter
     *
     * @param string $param
     *            Parameter name
     * @param mixed $default
     *            Default value
     * @return string Parameter value
     */
    public function getParam(string $param, $default = false)
    {
        return $this->getParamsFetcher()->getParam($param, $default);
    }

    /**
     * Formatting call stack
     *
     * @param mixed $e
     *            Exception object
     * @return array Call stack
     */
    protected function formatCallStack($e): array
    {
        $stack = $e->getTrace();

        foreach ($stack as $i => $call) {
            $stack[$i] = (@$call['file'] == '' ? 'lambda : ' : @$call['file'] . ' (' . $call['line'] . ') : ') .
                (@$call['class'] == '' ? '' : $call['class'] . '->') . $call['function'];
        }

        return $stack;
    }

    /**
     * Method runs router
     *
     * @codeCoverageIgnore
     */
    public function run(): void
    {
        print($this->router->callRoute($_GET['r']));
    }

    /**
     * Method processes exception
     *
     * @param \Exception $e
     *            Exception object
     * @codeCoverageIgnore
     */
    public function handleException($e): void
    {
        print('<pre>' . $e->getTraceAsString());
    }

    /**
     * Method fetches actions for routes
     *
     * @param \Mezon\Service\ServiceBaseLogicInterface $actionsSource
     *            Source of actions
     */
    public function fetchActions(\Mezon\Service\ServiceBaseLogicInterface $actionsSource): void
    {
        $methods = get_class_methods($actionsSource);

        foreach ($methods as $method) {
            if (strpos($method, 'action') === 0) {
                $route = \Mezon\Router\Utils::convertMethodNameToRoute($method);

                $this->router->addRoute(
                    $route,
                    function () use ($actionsSource, $method) {
                        return $this->callPublicLogic($actionsSource, $method, []);
                    },
                    'GET');

                $this->router->addRoute(
                    $route,
                    function () use ($actionsSource, $method) {
                        return $this->callPublicLogic($actionsSource, $method, []);
                    },
                    'POST');
            }
        }
    }

    /**
     * Method constructs request data fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface Request data fetcher
     */
    public function getParamsFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        if ($this->paramsFetcher !== false) {
            return $this->paramsFetcher;
        }

        return $this->paramsFetcher = $this->createFetcher();
    }

    /**
     * Method returns true if the router exists
     *
     * @param string $route
     *            checking route
     * @return bool true if the router exists, false otherwise
     */
    public function routeExists(string $route): bool
    {
        return $this->router->routeExists($route);
    }

    /**
     * Method returns router
     *
     * @return \Mezon\Router\Router router
     */
    public function &getRouter(): \Mezon\Router\Router
    {
        return $this->router;
    }
}
