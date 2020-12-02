<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\Transport;

class TestingTransport extends ServiceConsoleTransport
{

    /**
     * 
     * {@inheritDoc}
     * @see Transport::die()
     */
    protected function die(): void
    {
        // nop
    }
}
