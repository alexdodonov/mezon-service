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
     * @var object Service transport object
     */
    private $serviceTransport = false;

    /**
     * Service's logic
     *
     * @var array Login object or list of logic objects
     */
    private $serviceLogic = false;

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
        $serviceLogic = ServiceBaseLogic::class,
        $serviceModel = ServiceModel::class,
        $securityProvider = \Mezon\Security\MockProvider::class,
        $serviceTransport = ServiceRestTransport\ServiceRestTransport::class)
    {
        try {
            $this->initTransport($serviceTransport, $securityProvider);

            $this->initServiceLogic($serviceLogic, $serviceModel);

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

        foreach ($this->serviceLogic as $actionsSet) {
            if ($actionsSet instanceof ServiceBaseLogicInterface) {
                $this->serviceTransport->fetchActions($actionsSet);
            }
        }
    }

    /**
     * Method inits service's transport
     *
     * @param mixed $serviceTransport
     *            Service's transport
     * @param mixed $securityProvider
     *            Service's security provider
     */
    protected function initTransport($serviceTransport, $securityProvider): void
    {
        if (is_string($serviceTransport)) {
            $this->serviceTransport = new $serviceTransport($securityProvider);
        } else {
            $this->serviceTransport = $serviceTransport;
        }
    }

    /**
     * Method constructs service logic if necessary
     *
     * @param mixed $serviceLogic
     *            Service logic class name of object itself
     * @param mixed $serviceModel
     *            Service model class name of object itself
     * @return ServiceLogic logic object
     */
    protected function constructServiceLogic($serviceLogic, $serviceModel)
    {
        if (is_string($serviceLogic)) {
            $result = new $serviceLogic(
                $this->serviceTransport->getParamsFetcher(),
                $this->serviceTransport->getSecurityProvider(),
                $serviceModel);
        } else {
            $result = $serviceLogic;
        }

        return $result;
    }

    /**
     * Method inits service's logic
     *
     * @param mixed $serviceLogic
     *            Service's logic
     * @param mixed $serviceModel
     *            Service's Model
     */
    protected function initServiceLogic($serviceLogic, $serviceModel): void
    {
        if (is_array($serviceLogic)) {
            $this->serviceLogic = [];

            foreach ($serviceLogic as $logic) {
                $this->serviceLogic[] = $this->constructServiceLogic($logic, $serviceModel);
            }
        } else {
            $this->serviceLogic = [
                $this->constructServiceLogic($serviceLogic, $serviceModel)
            ];
        }

        $this->serviceTransport->setServiceLogic($this->serviceLogic);
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
     * @return Transport
     */
    public function getTransport(): Transport
    {
        return $this->serviceTransport;
    }

    /**
     * Method returns logic
     *
     * @return ServiceLogic|array
     */
    public function getLogic()
    {
        return $this->serviceLogic;
    }
}
