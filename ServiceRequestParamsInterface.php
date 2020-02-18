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
interface ServiceRequestParamsInterface
{

    /**
     * Method returns request parameter
     *
     * @param string $param
     *            parameter name
     * @param mixed $default
     *            default value
     * @return mixed Parameter value
     */
    public function getParam($param, $default = false);
}
