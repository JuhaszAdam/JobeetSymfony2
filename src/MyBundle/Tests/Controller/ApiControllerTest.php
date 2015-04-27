<?php

namespace MyBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\Stopwatch\Stopwatch;

class ApiControllerTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var Stopwatch
     */
    private $stopwatch;


    public function setUp()
    {
        $this->stopwatch = new Stopwatch();
        $event = $this->stopwatch->start('setUp');

        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->application = new Application(static::$kernel);

        $this->dropDatabase();
        $this->createDatabase();
        $this->createSchema();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->loadFixtures();

        $event->stop();
        echo "setUp : " . $event->getDuration() . " ms" . PHP_EOL;
    }

    /**
     * @throws \Exception
     */
    private function dropDatabase()
    {
        $command = new DropDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true
        ));
        $command->run($input, new NullOutput());
        $connection = $this->application->getKernel()->getContainer()->get('doctrine')->getConnection();
        if ($connection->isConnected()) {
            $connection->close();
        }
    }

    /**
     * @throws \Exception
     */
    private function createDatabase()
    {
        $command = new CreateDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
        ));
        $command->run($input, new NullOutput());
    }

    /**
     * @throws \Exception
     */
    private function createSchema()
    {
        $command = new CreateSchemaDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:create',
        ));
        $command->run($input, new NullOutput());
    }

    private function loadFixtures()
    {
        $client = static::createClient();
        $loader = new ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(static::$kernel->locateResource('@MyBundle/DataFixtures/ORM'));
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testList()
    {
        $event = $this->stopwatch->start('testList');

        $client = static::createClient();
        $crawler = $client->request('GET', '/api/sensio-labs/jobs.xml');

        $this->assertEquals('MyBundle\Controller\ApiController::listAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertTrue($crawler->filter('description')->count() == 31);
        $crawler = $client->request('GET', '/api/sensio-labs87/jobs.xml');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/symfony/jobs.xml');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/sensio-labs/jobs.json');
        $this->assertEquals('MyBundle\Controller\ApiController::listAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertRegExp('/"category":"Programming"/', $client->getResponse()->getContent());
        $crawler = $client->request('GET', '/api/sensio-labs87/jobs.json');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/sensio-labs/jobs.yaml');
        $this->assertRegExp('/category: Programming/', $client->getResponse()->getContent());
        $this->assertEquals('MyBundle\Controller\ApiController::listAction', $client->getRequest()->attributes->get('_controller'));
        $crawler = $client->request('GET', '/api/sensio-labs87/jobs.yaml');
        $this->assertTrue(404 === $client->getResponse()->getStatusCode());

        $event->stop();
        echo "testList : " . $event->getDuration() . " ms" . PHP_EOL;
    }
}
