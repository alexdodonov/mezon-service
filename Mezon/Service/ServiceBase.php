<?php
namespace Mezon\Service;

/**
 * Class Service
 *
 * @package Mezon
 * @subpackage ServiceBase
 * @author Dodonov A.A.
 * @version v.1.0 (2019/12/09)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base service class
 *
 * It bounds together transport, request parameters fetcher, logic, authorization and model
 *
 * @author Dodonov A.A.
 */
class ServiceBase
{

    /**
     * Service's ransport
     *
     * @var TransportInterface service transport object
     */
    private $serviceTransport;

    /**
     * Constructor
     *
     * @param TransportInterface $serviceTransport
     *            service's transport
     */
    public function __construct(TransportInterface $serviceTransport)
    {
        try {
            $this->serviceTransport = $serviceTransport;

            $this->initCustomRoutes();

            $this->fetchActions();
        } catch (\Exception $e) {
            $this->getTransport()->handleException($e);
        }
    }

    /**
     * Method fetches actions if they are existing
     */
    protected function fetchActions(): void
    {
        if ($this instanceof ServiceBaseLogicInterface) {
            $this->serviceTransport->fetchActions($this);
        }

        // TODO move to the Transport class
        foreach ($this->serviceTransport->getServiceLogics() as $actionsSet) {
            if ($actionsSet instanceof ServiceBaseLogicInterface) {
                $this->serviceTransport->fetchActions($actionsSet);
            }
        }
    }

    /**
     * Method inits custom routes if necessary
     */
    protected function initCustomRoutes(): void
    {
        $reflector = new \ReflectionClass(get_class($this));
        $classPath = dirname($reflector->getFileName());

        // TODO make /Conf/...
        if (file_exists($classPath . '/conf/routes.php')) {
            $this->serviceTransport->loadRoutesFromConfig($classPath . '/conf/routes.php');
        }

        if (file_exists($classPath . '/conf/routes.json')) {
            $this->serviceTransport->loadRoutes(json_decode(file_get_contents($classPath . '/conf/routes.json'), true));
        }
    }

    /**
     * Running $this->serviceTransport run loop
     */
    public function run(): void
    {
        $this->serviceTransport->run();
    }

    /**
     * Method sets transport
     *
     * @param Transport $transport
     */
    public function setTransport(Transport $transport): void
    {
        $this->serviceTransport = $transport;
    }

    /**
     * Method returns transport
     *
     * @return TransportInterface transport
     */
    public function getTransport(): TransportInterface
    {
        return $this->serviceTransport;
    }
}
