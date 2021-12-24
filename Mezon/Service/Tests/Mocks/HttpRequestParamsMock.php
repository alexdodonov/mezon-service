<?php
namespace Mezon\Service\Tests\Mocks;

use Mezon\Transport\HttpRequestParams;
use Mezon\Router\Router;

class HttpRequestParamsMock extends HttpRequestParams
{

    // TODO move to the mezon/transport package
    /**
     * Constructor
     */
    public function __construct()
    {
        $router = new Router();

        parent::__construct($router);
    }

    /**
     *
     * {@inheritdoc}
     * @see HttpRequestParams::getParam()
     */
    public function getParam($param, $default = false)
    {
        return 'token';
    }
}
