<?php

namespace LLS\Bundle\SQSBundle\Tests\Utils;

use mageekguy\atoum\test;
use Aws\Common\Client\ClientBuilder;
use Aws\Common\Enum\ClientOptions as Options;
use Aws\Sqs\QueueUrlListener;
use Aws\Sqs\Md5ValidatorListener;
use Guzzle\Plugin\Mock\MockPlugin;

use LLS\Bundle\AWSBundle\Interfaces\IdentityInterface;

/**
 * Test class for Aws Services
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
abstract class AwsServiceTest extends test
{
    protected $guzzleMock;

    public function beforeTestMethod ($method)
    {
        $this->guzzleMock = new MockPlugin(array());
    }

    protected $config = array(
        'key'    => '123',
        'secret' => 'blabla',
        'region' => 'eu-west-1'
    );

    protected function getClientFactoryInterfaceMock()
    {
        $mock = new \mock\LLS\Bundle\AWSBundle\Interfaces\ClientFactoryInterface();
        $config = $this->config;

        $mock->getMockController()->createClient = function ($type, IdentityInterface $identity) use ($config) {
            $reflection = new \ReflectionClass("Aws\\Sqs\\SqsClient");
            $file = $reflection->getFileName();
            
            $client = ClientBuilder::factory("mock\\Aws\\Sqs")
                ->setConfig($config)
                ->setConfigDefaults(array(
                    Options::VERSION => \Aws\Sqs\SqsClient::LATEST_API_VERSION,
                    Options::SERVICE_DESCRIPTION => dirname($file) . '/Resources/sqs-%s.php'
                ))
                ->build();

            $client->addSubscriber(new QueueUrlListener());
            $client->addSubscriber(new Md5ValidatorListener());

            return $client;
        };

        return $mock;
    }

    protected function getIdentityInterfaceMock()
    {
        return new \mock\LLS\Bundle\AWSBundle\Interfaces\IdentityInterface();
    }

    public function fixtureGuzzleCall($route)
    {
        $response = MockPlugin::getMockFile(__DIR__.'/../Fixtures/GuzzleResponses/'.$route.'.txt');

        $this->guzzleMock->addResponse($response);

        return $this;
    }
}