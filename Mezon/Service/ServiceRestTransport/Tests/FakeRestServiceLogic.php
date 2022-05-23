<?php
namespace Mezon\Service\ServiceRestTransport\Tests;

use Mezon\Service\ServiceLogic;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceModel;
use Mezon\Transport\Tests\MockParamsFetcher;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;

/**
 * Fake service logic.
 *
 * @author Dodonov A.A.
 */
class FakeRestServiceLogic extends ServiceLogic
{

    /**
     * Some fake transport
     *
     * @var ServiceRestTransport
     */
    var $transport;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->transport = new ServiceRestTransport();

        parent::__construct(new MockParamsFetcher(), new MockProvider(), new ServiceModel());
    }

    /**
     * Method wich throws exception
     */
    public function exception(): void
    {
        throw (new \Exception('Exception', - 1));
    }
}
