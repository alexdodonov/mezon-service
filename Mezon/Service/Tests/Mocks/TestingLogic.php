<?php
namespace Mezon\Service\Tests\Mocks;

use Mezon\Service\ServiceLogic;
use Mezon\Service\ServiceActionsInterface;

/**
 * The file contains testing classes.
 */

/**
 *
 * @author Dodonov A.A.
 */
class TestingLogic extends ServiceLogic implements ServiceActionsInterface
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

    /**
     * Logic from action
     *
     * @return string action result
     */
    public function actionTest4(): string
    {
        return 'test4';
    }
}
