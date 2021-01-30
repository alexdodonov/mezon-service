<?php
namespace Mezon\Service;

use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Security\MockProvider;

/**
 * Class Service
 *
 * @package Mezon
 * @subpackage Service
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Service class
 *
 * It bounds together transport, request parameters fetcher, logic, authorization and model
 *
 * @author Dodonov A.A.
 */
class Service extends ServiceBase
{

    /**
     * Constructor
     *
     * @param mixed $serviceLogic
     *            Service's logic
     * @param mixed $serviceModel
     *            Service's model
     * @param mixed $securityProvider
     *            Service's security provider
     * @param mixed $serviceTransport
     *            Service's transport
     */
    public function __construct(
        $serviceLogic = ServiceLogic::class,
        $serviceModel = ServiceModel::class,
        $securityProvider = MockProvider::class,
        $serviceTransport = ServiceRestTransport::class)
    {
        try {
            parent::__construct($serviceLogic, $serviceModel, $securityProvider, $serviceTransport);

            $this->initCommonRoutes();
        } catch (\Exception $e) {
            $this->getTransport()->handleException($e);
        }
    }

    /**
     * Method inits common servoce's routes
     */
    protected function initCommonRoutes(): void
    {
        $this->getTransport()->addRoute('/connect/', 'connect', 'POST', 'public_call');
        $this->getTransport()->addRoute('/token/[a:token]/', 'setToken', 'POST');
        $this->getTransport()->addRoute('/self/id/', 'getSelfId', 'GET');
        $this->getTransport()->addRoute('/self/login/', 'getSelfLogin', 'GET');
        $this->getTransport()->addRoute('/login-as/', 'loginAs', 'POST');
    }

    /**
     * Method launches service
     *
     * @param Service|string $service
     *            name of the service class or the service object itself
     * @param ServiceLogic|string $serviceLogic
     *            Logic of the service
     * @param ServiceModel|string $serviceModel
     *            Model of the service
     * @param ServiceSecurityProviderInterface|string $securityProvider
     *            name of the service security provider class or the service security provider itself
     * @param Transport|string $serviceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $runService
     *            Shold be service lanched
     * @return Service Created service
     * @deprecated See Service::run
     */
    public static function launch(
        $service,
        $serviceLogic = ServiceLogic::class,
        $serviceModel = ServiceModel::class,
        $securityProvider = MockProvider::class,
        $serviceTransport = ServiceRestTransport::class,
        bool $runService = true): ServiceBase
    {
        if (is_string($service)) {
            $service = new $service($serviceLogic, $serviceModel, $securityProvider, $serviceTransport);
        }

        if ($runService === false) {
            return $service;
        }

        $service->run();

        return $service;
    }

    /**
     * Method launches service
     *
     * @param Service|string $service
     *            name of the service class or the service object itself
     * @param ServiceLogic|string $serviceLogic
     *            Logic of the service
     * @param ServiceModel|string $serviceModel
     *            Model of the service
     * @param ServiceSecurityProviderInterface|string $securityProvider
     *            name of the service security provider class or the service security provider itself
     * @param Transport|string $serviceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $runService
     *            Shold be service lanched
     * @return Service Created service
     */
    public static function start(
        $service,
        $serviceLogic = ServiceLogic::class,
        $serviceModel = ServiceModel::class,
        $securityProvider = MockProvider::class,
        $serviceTransport = ServiceRestTransport::class,
        bool $runService = true): ServiceBase
    {
        if (is_string($service)) {
            $service = new $service($serviceLogic, $serviceModel, $securityProvider, $serviceTransport);
        }

        if ($runService === false) {
            return $service;
        }

        $service->run();

        return $service;
    }
}
