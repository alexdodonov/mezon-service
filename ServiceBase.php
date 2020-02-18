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
    protected $serviceTransport = false;

    /**
     * Service's logic
     *
     * @var \Mezon\Service\ServiceLogic|array Login object or list of logic objects
     */
    protected $serviceLogic = false;

    /**
     * Constructor
     *
     * @param mixed $serviceTransport
     *            Service's transport
     * @param mixed $securityProvider
     *            Service's security provider
     * @param mixed $serviceLogic
     *            Service's logic
     * @param mixed $serviceModel
     *            Service's model
     */
    public function __construct(
        $serviceTransport = \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
        $securityProvider = \Mezon\Service\ServiceMockSecurityProvider::class,
        $serviceLogic = \Mezon\Service\ServiceBaseLogic::class,
        $serviceModel = \Mezon\Service\ServiceModel::class)
    {
        $this->initTransport($serviceTransport, $securityProvider);

        $this->initServiceLogic($serviceLogic, $serviceModel);

        $this->initCustomRoutes();

        if ($this instanceof \Mezon\Service\ServiceBaseLogicInterface) {
            $this->serviceTransport->fetchActions($this);
        }

        if ($this->serviceLogic instanceof \Mezon\Service\ServiceBaseLogicInterface) {
            $this->serviceTransport->fetchActions($this->serviceLogic);
        } elseif (is_array($this->serviceLogic)) {
            foreach ($this->serviceLogic as $actionsSet) {
                if ($actionsSet instanceof \Mezon\Service\ServiceBaseLogicInterface) {
                    $this->serviceTransport->fetchActions($actionsSet);
                }
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
     * @return \Mezon\Service\ServiceLogic logic object
     */
    protected function constructServiceLogic($serviceLogic, $serviceModel)
    {
        if (is_string($serviceLogic)) {
            $result = new $serviceLogic(
                $this->serviceTransport->getParamsFetcher(),
                $this->serviceTransport->securityProvider,
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
            $this->serviceLogic = $this->constructServiceLogic($serviceLogic, $serviceModel);
        }

        $this->serviceTransport->serviceLogic = $this->serviceLogic;
    }

    /**
     * Method inits custom routes if necessary
     */
    protected function initCustomRoutes(): void
    {
        $reflector = new \ReflectionClass(get_class($this));
        $classPath = dirname($reflector->getFileName());

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
     * @param \Mezon\Service\ServiceTransport $transport
     */
    public function setTransport(\Mezon\Service\ServiceTransport $transport): void
    {
        $this->serviceTransport = $transport;
    }

    /**
     * Method returns transport
     *
     * @return \Mezon\Service\ServiceTransport
     */
    public function getTransport(): \Mezon\Service\ServiceTransport
    {
        return $this->serviceTransport;
    }

    /**
     * Method returns logic
     * 
     * @return \Mezon\Service\ServiceLogic|array
     */
    public function getLogic()
    {
        return $this->serviceLogic;
    }
}
