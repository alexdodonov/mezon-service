<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceActionsInterface;

class TestingBaseService extends ServiceBase implements ServiceActionsInterface
{

    public function actionTest3(): string
    {
        return 'Action!';
    }
}
