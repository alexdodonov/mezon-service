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
     * Method inits common service's routes
     */
    protected function initCommonRoutes(): void
    {
        // TODO create these methods in the Transport module, this is transport's responsibility
        // TODO in ServiceBaseLogic::__construct remove parameters $paramsFetcher and $securityProvider and make setters. It will allow us to avoid
        // code like this:
        // $serviceLogic = new Logic($serviceTransport->getParamsFetcher(), $serviceTransport->getSecurityProvider());
        // $serviceTransport->setServiceLogic($serviceLogic);
        // and setup $paramsFetcher and $securityProvider in this call $serviceTransport->setServiceLogic($serviceLogic);
        // this will make our code more neat and short
        // TODO make Transport accept null instead of $securityProvider - in this way no security is provided for the implemented logic, this will 
        // help us to make code shortener and avoid creating $securityProvider = new MockProvider(); wich does nothing
        $this->getTransport()->addRoute('/connect/', 'connect', 'POST', 'public_call');
        $this->getTransport()->addRoute('/token/[a:token]/', 'setToken', 'POST');
        $this->getTransport()->addRoute('/self/id/', 'getSelfId', 'GET');
        $this->getTransport()->addRoute('/self/login/', 'getSelfLogin', 'GET');
        $this->getTransport()->addRoute('/login-as/', 'loginAs', 'POST');
    }
}
