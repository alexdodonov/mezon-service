<?php
namespace Mezon\Service;

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
class Service extends \Mezon\Service\ServiceBase
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
        $serviceLogic = \Mezon\Service\ServiceLogic::class,
        $serviceModel = \Mezon\Service\ServiceModel::class,
        $securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $serviceTransport = \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class)
    {
        parent::__construct($serviceLogic, $serviceModel, $securityProvider, $serviceTransport);

        $this->initCommonRoutes();
    }

    /**
     * Method inits common servoce's routes
     */
    protected function initCommonRoutes(): void
    {
        $this->serviceTransport->addRoute('/connect/', 'connect', 'POST', 'public_call');
        $this->serviceTransport->addRoute('/token/[a:token]/', 'setToken', 'POST');
        $this->serviceTransport->addRoute('/self/id/', 'getSelfId', 'GET');
        $this->serviceTransport->addRoute('/self/login/', 'getSelfLogin', 'GET');
        $this->serviceTransport->addRoute('/login-as/', 'loginAs', 'POST');
    }

    /**
     * Method launches service
     *
     * @param Service|string $service
     *            name of the service class or the service object itself
     * @param \Mezon\Service\ServiceLogic|string $serviceLogic
     *            Logic of the service
     * @param \Mezon\Service\ServiceModel|string $serviceModel
     *            Model of the service
     * @param \Mezon\Service\ServiceSecurityProviderInterface|string $securityProvider
     *            name of the service security provider class or the service security provider itself
     * @param \Mezon\Service\ServiceTransport|string $serviceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $runService
     *            Shold be service lanched
     * @return \Mezon\Service\Service Created service
     * @deprecated See Service::run
     */
    public static function launch(
        $service,
        $serviceLogic = \Mezon\Service\ServiceLogic::class,
        $serviceModel = \Mezon\Service\ServiceModel::class,
        $securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $serviceTransport = \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
        bool $runService = true): \Mezon\Service\ServiceBase
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
     * @param \Mezon\Service\ServiceLogic|string $serviceLogic
     *            Logic of the service
     * @param \Mezon\Service\ServiceModel|string $serviceModel
     *            Model of the service
     * @param \Mezon\Service\ServiceSecurityProviderInterface|string $securityProvider
     *            name of the service security provider class or the service security provider itself
     * @param \Mezon\Service\ServiceTransport|string $serviceTransport
     *            name of the service transport class or the service transport itself
     * @param bool $runService
     *            Shold be service lanched
     * @return \Mezon\Service\Service Created service
     */
    public static function start(
        $service,
        $serviceLogic = \Mezon\Service\ServiceLogic::class,
        $serviceModel = \Mezon\Service\ServiceModel::class,
        $securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $serviceTransport = \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
        bool $runService = true): \Mezon\Service\ServiceBase
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
