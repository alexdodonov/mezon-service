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

## Introducing logic

In the concept of Mezon framework service class is only a container for logic, model, transport, security providerrs and so on.

So we may want to fetch all logic to the separate class. Then we shall do it in this way:

```PHP
/**
 * Logic implementation
 *
 * @author Dodonov A.A.
 */
class TodoLogic extends \Mezon\Service\ServiceBaseLogic
{

    /**
     * First endpoint
     */
    public function actionPing()
    {
        return ('I am alive!');
    }
}
```

And then we shall modify service class like this:

```PHP
/**
 * Service class
 *
 * @author Dodonov A.A.
 */
class TodoService extends \Mezon\Service\ServiceBase
{
}

\Mezon\Service\Service::start(TodoService::class, TodoLogic::class);
```

But as you see - we have empty service class with only base functionality. So we can completely remove it and change our code:

```PHP
\Mezon\Service\Service::start(\Mezon\Service\ServiceBase::class, TodoLogic::class);
```

## Multyple logic classes

But you can split your functionality into several classes like in the next example:

```PHP
\Mezon\Service\Service::start(\Mezon\Service\ServiceBase::class, [
    TodoSystemLogic::class,
    TodoReadLogic::class,
    TodoWriteLogic::class
]);
```

Here you just pass several classes when creating service.