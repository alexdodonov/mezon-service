<?php
namespace Mezon\Service;

use Mezon\Security\ProviderInterface;

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
     * @param TransportInterface $serviceTransport
     *            Service's transport
     */
    public function __construct(TransportInterface $serviceTransport)
    {
        try {
            parent::__construct($serviceTransport);

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
}
