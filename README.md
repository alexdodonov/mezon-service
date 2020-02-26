# Set of classes for creating microservices [![Build Status](https://travis-ci.com/alexdodonov/mezon-service.svg?branch=master)](https://travis-ci.com/alexdodonov/mezon-service) [![codecov](https://codecov.io/gh/alexdodonov/mezon-service/branch/master/graph/badge.svg)](https://codecov.io/gh/alexdodonov/mezon-service)

## Installation

Just type

```
composer require mezon/service
```

## First step

This is our first service.

```PHP
class TodoService extends \Mezon\Service\ServiceBase implements \Mezon\Service\ServiceBaseLogicInterface
{

    /**
     * First endpoint
     */
    public function actionPing()
    {
        return ('I am alive!');
    }
}

\Mezon\Service\Service::start(TodoService::class);
```

Here:

- \Mezon\Service\ServiceBase - base class for simple services
- \Mezon\Service\ServiceBaseLogicInterface - class must implement this interface to provide actions in format 'action<Endpoint>'
- \Mezon\Service\Service::start(TodoService::class) - launching our service

Then you can access your first endpoint in a way like this:

```
http://localhost/?r=ping
```

Here is 'ping' is part afer 'action' in the 'actionPing'.

You can create longer endpoints:

```PHP
public function actionLongEndpoint()
{
    return ('long endpoint');
}
```

And it will be available via this URL:

```
http://localhost/?r=long-enpoint
```