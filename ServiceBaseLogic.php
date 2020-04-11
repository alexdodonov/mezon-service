<?php
namespace Mezon\Service;

/**
 * Class ServiceBaseLogic
 *
 * @package Service
 * @subpackage ServiceBaseLogic
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class stores all service's logic
 *
 * @author Dodonov A.A.
 */
class ServiceBaseLogic implements \Mezon\Service\ServiceBaseLogicInterface
{

    /**
     * Security provider
     *
     * @var \Mezon\Service\ServiceSecurityProviderInterface
     */
    protected $securityProvider = null;

    /**
     * Request params fetcher
     */
    protected $paramsFetcher = false;

    /**
     * Model
     *
     * @var \Mezon\Service\ServiceModel
     */
    protected $model = false;

    /**
     * Constructor
     *
     * @param \Mezon\Service\ServiceRequestParamsInterface $paramsFetcher
     *            Params fetcher
     * @param \Mezon\Service\ServiceSecurityProviderInterface $securityProvider
     *            Security provider
     * @param mixed $model
     *            Service model
     */
    public function __construct(
        \Mezon\Service\ServiceRequestParamsInterface $paramsFetcher,
        \Mezon\Service\ServiceSecurityProviderInterface $securityProvider,
        $model = null)
    {
        $this->paramsFetcher = $paramsFetcher;

        $this->securityProvider = $securityProvider;

        if (is_string($model)) {
            $this->model = new $model();
        } else {
            $this->model = $model;
        }
    }

    /**
     * Method returns request parameter
     *
     * @param string $param
     *            parameter name
     * @param mixed $default
     *            default value
     * @return mixed Parameter value
     */
    protected function getParam($param, $default = false)
    {
        return $this->getParamsFetcher()->getParam($param, $default);
    }

    /**
     * Method returns model object
     *
     * @return ?\Mezon\Service\ServiceModel Model
     */
    public function getModel(): ?\Mezon\Service\ServiceModel
    {
        return $this->model;
    }

    /**
     * Method return params fetcher
     *
     * @return \Mezon\Service\ServiceRequestParamsInterface Params fetcher
     */
    public function getParamsFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return $this->paramsFetcher;
    }

    /**
     * Method returns security provider
     *
     * @return \Mezon\Service\ServiceSecurityProviderInterface
     */
    public function getSecurityProvider(): \Mezon\Service\ServiceSecurityProviderInterface
    {
        return $this->securityProvider;
    }
}
