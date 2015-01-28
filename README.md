[![LeLivreScolaire](http://h2010.associationhec.com/images/news/logo-officiel-jpeg.jpg)](http://www.lelivrescolaire.fr)

# *SQS Bundle* [![Build Status](https://secure.travis-ci.org/lelivrescolaire/SQSBundle.png?branch=master)](http://travis-ci.org/lelivrescolaire/SQSBundle) [![Coverage Status](https://coveralls.io/repos/lelivrescolaire/SQSBundle/badge.png?branch=master)](https://coveralls.io/r/lelivrescolaire/SQSBundle?branch=master)

Communicate with an SQS queue from inside your symfony 2 application.

This bundle is an extension of [lelivrescolaire/AWSBundle](https://github.com/lelivrescolaire/AWSBundle).

## Installation

```shell
$ composer require "lelivrescolaire/sqs-bundle:dev-master"
```

AppKernel:

```php
public function registerBundles()
{
    $bundles = array(
        new LLS\Bundle\AWSBundle\LLSAWSBundle(),
        new LLS\Bundle\SQSBundle\LLSSQSBundle(),
    );
}
```

## Configuration reference

```yml
llsaws:
    identities:
        my_identity:                        # Arbitrary Identity service name
            type: user                      # Identity type name (factory alias)
            fields:                         # Identity fields
                key: '<user AWS key>'
                secret: '<user AWS secret>'
    services:
        my_sqs_service:                     # Arbitrary service name
            type: sqs                       # Service Type (factory alias)
            identity: my_identity

llssqs:
    queues:
        my_queue:                   # Arbitrary Queue service name
            service: my_sqs_service # SQS Service name
            name: myQueue           # AWS Queue name
```

## Usage

Given the previous config:

```php
<?php
namespace Acme\Bundle\MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use LLS\Bundle\SQSBundle\Model\Queue;
use LLS\Bundle\SQSBundle\Model\Message;

class MyController extends Controller
{
    public function myAction()
    {
        $identityAsService = $this->get('llsaws.identities.my_identity');
        $sqsAsService      = $this->get('llsaws.services.my_sqs_service');
        $queueAsService    = $this->get('llssqs.queues.my_queue');

        // Create a queue

        $queue = $sqsAsService->getQueue('myCreatedQueue'); // Instanciate Queue

        $sqsAsService->createQueue($queue); // Remotely create the queue

        var_dump($queue->getUrl()); // Get queue URL

        // Send a message to a queue

        $message = new Message();
        $message->setBody('Hello world!');

        $queueAsService->sendMessage($message);

        // Fetch messages from a queue

        $maxMsg = 10; // Max number of messages to fetch

        $messages = $queueAsService->fetchMessages($maxMsg);

        foreach ($messages as $message) {
            var_dump($message->getBody());
            $queueAsService->delete($message); // Delete message (only works for fetched Messages)
        }
    }
}
```

## Ship Monolog logs to SQS

See [here](https://github.com/lelivrescolaire/MonologExtraBundle/blob/master/Resources/doc/Handlers/sqs_handler.md).

## Contribution

Feel free to send us [Pull Requests](https://github.com/lelivrescolaire/SQSBundle/compare) and [Issues](https://github.com/lelivrescolaire/SQSBundle/issues/new) with your fixs and features.

## Run test

### Unit tests

```shell
$ ./bin/atoum
```

### Coding standards

```shell
$ ./bin/coke
```
