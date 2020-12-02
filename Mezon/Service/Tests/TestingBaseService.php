<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceBaseLogicInterface;

class TestingBaseService extends ServiceBase implements ServiceBaseLogicInterface
{

    public function actionTest(): string
    {
        return 'Action!';
    }

    protected function initCustomRoutes(): void
    {
        // we don't need to load custom routes
    }
}
