<?php
namespace Mezon\Service;

/**
 * Interface ServiceRequestParamsInterface
 *
 * @package Service
 * @subpackage ServiceRequestParamsInterface
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/31)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
abstract class ServiceRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

    /**
     * Router of the transport
     *
     * @var \Mezon\Router\Router
     */
    protected $router = false; // TODO make it private

    /**
     * Constructor
     *
     * @param \Mezon\Router\Router $router
     *            Router object
     */
    public function __construct(\Mezon\Router\Router &$router)
    {
        $this->router = $router;
    }
}
