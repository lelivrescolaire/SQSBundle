# SQS Bundle - LeLivreScolaire

Communicate with an SQS queue from inside your symfony 2 application.

## Features

* Ship logs from your application directly to SQS queue using Monolog

## Installation

```shell
$ composer require "lelivrescolaire/sqs-bundle:dev-master"
```

AppKernel:

```php
public function registerBundles()
{
    $bundles = array(
        new LLS\Bundle\SQSBundle\LLSSQSBundle(),
        new Cybernox\AmazonWebServicesBundle\CybernoxAmazonWebServicesBundle(),
    );
}
```

## Configuration reference

```yml
cybernox_amazon_web_services:
    key:              %aws_key%
    secret:           %aws_secret%

monolog:
    handlers:
        sqs:
            type:     service
            id:       lls_sqs_handler
            priority: 0

llssqs:
    monolog:
        handler:
            queueUrl: "%sqs_queue_url%"
            level:    INFO
            bubble:   true
```

`queueUrl`: Your SQS Queue URL (required)

`level`: Min log level to ship to SQS (Optional, default: INFO)

`bubble`: Let other handlers handle this message (Optional, default: true)

## Usage

Nothing to do, once properly configured, Monolog wil automatically ship logs to your SQS Queue.

## Contribution

Feel free to send us Pull Requests with your fixs and features.