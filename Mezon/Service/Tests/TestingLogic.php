<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceLogic;

/**
 * The file contains testing classes.
 */

/**
 *
 * @author Dodonov A.A.
 */
class TestingLogic extends ServiceLogic
{

    public function test(): string
    {
        return 'test';
    }

    public function test2(): string
    {
        return 'test2';
    }

    /**
     * Method creates connection
     *
     * @return array session id
     */
    public function connect(): array
    {
        return [
            'session-id' => 'connect'
        ];
    }
}
