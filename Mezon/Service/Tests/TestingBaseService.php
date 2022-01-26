<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceBaseLogicInterface;

class TestingBaseService extends ServiceBase implements ServiceBaseLogicInterface
{

    public function actionTest3(): string
    {
        return 'Action!';
    }
}
