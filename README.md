# Set of classes for creating microservices
[![Build Status](https://travis-ci.com/alexdodonov/mezon-service.svg?branch=master)](https://travis-ci.com/alexdodonov/mezon-service) [![codecov](https://codecov.io/gh/alexdodonov/mezon-service/branch/master/graph/badge.svg)](https://codecov.io/gh/alexdodonov/mezon-service) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexdodonov/mezon-service/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexdodonov/mezon-service/?branch=master)

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

## Complex routing

You can create more complex routes. To do this you have to setup them in the routes.json config of your service. The content of this file must looks like this:

```JS
[
	{
		"route": "/user/[s:userName]/profile/articles/[i:articleId]/comments/[i:headCommentId]",
		"callback": "userHeadComment",
		"method": "GET",
		"call_type": "public_call"
	}
]
```

And we need logic class for this route:

```PHP
class CommentLogic extends \Mezon\Service\ServiceBaseLogic
{

    /**
     * Our endpoint
     */
    public function userHeadComment(string $route, array $params)
    {
        return [
            // some data here
        ];
    }
}
```

In this example the method userHeadComment handles routing processing. And this method receives array with routes parameters. Something like that:

```PHP
[
    'userName' => 'name of the user',
    'articleId' => 'id of the article',
    'headCommentId' => 'comment's id'
]
```

But you can also store all route configs in PHP files like this:

```PHP
<?php

return [
    [
    	"route" => "/user/[s:userName]/profile/articles/[i:articleId]/comments/[i:headCommentId]",
		"callback" => "userHeadComment",
		"method" => "GET",
		"call_type" => "public_call"
    ]
];
```

## Authentication

It is quite obvious that not all your endpoints will be public. Some of them should check credentials.

First of all you should understand that framework knows nothing about your registry of users and their permissions and roles. So you have to provide information about that.

Do it by implementing security provider like inthe listing below:

```PHP
// this is first step
// but we have some more )
class TodoSecurityProvider extends \Mezon\Service\ServiceAuthenticationSecurityProvider{
    
}
```

And our code will look like this:

```PHP
/**
 * Logic implementation
 */
class TodoLogic extends \Mezon\Service\ServiceBaseLogic
{

    /**
     * First endpoint
     */
    public function ping(): string
    {
        return 'I am alive!';
    }

    /**
     * Second route
     */
    public function whoAmI(): string
    {
        return 'I\'am Batman!';
    }
}

/**
 * Service class
 */
class TodoService extends \Mezon\Service\ServiceBase
{
}

/**
 * Security provider
 */
class TodoSecurityProvider extends \Mezon\Service\ServiceAuthenticationSecurityProvider{}

\Mezon\Service\Service::start(TodoService::class, TodoLogic::class, null, TodoSecurityProvider::class);
```

And routes must be described like this:

```JS
[
	{
		"route": "/ping/",
		"callback": "ping",
		"method": "GET",
		"call_type": "public_call"
	},
	{
		"route": "/who-am-i/",
		"callback": "whoAmI",
		"method": "GET"
	}
]
```

## Custom fields

You can extend your models with fields wich configuration will be defined in the client's code. For example you have entity 'user' with some custom fields. But you don't know what custom fields it will have. You don't know will it have field 'skype', or field 'notes', or field 'gender' etc. List of these fields may vary from project to project.

To work with such fields you can use \Mezon\Service\CustomFieldsModel

### Table structure

tba

### Methods reference

tba