<?php
namespace Mezon\Service\Tests;

/**
 * Mock parameter fetcher
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class MockParamsFetcher implements \Mezon\Service\ServiceRequestParamsInterface
{

    /**
     * Some testing value
     *
     * @var string
     */
    protected $value = false;

    /**
     * Constructor
     *
     * @param string $value
     *            Value to be set
     */
    public function __construct($value = 'value')
    {
        $this->value = $value;
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
    public function getParam($param, $default = false)
    {
        return $this->value;
    }
}
