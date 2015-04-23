<?php

namespace jobeet\MyBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Symfony\Component\Stopwatch\Stopwatch;

class AffiliateControllerTest extends WebTestCase
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

    public function testAffiliateForm()
    {
        $event = $this->stopwatch->start('testAffilateForm');

        $client = static::createClient();
        $crawler = $client->request('GET', '/affiliate/new');

        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::newAction', $client->getRequest()->attributes->get('_controller'));

        $form = $crawler->selectButton('Submit')->form(array(
            'affiliate[url]' => 'http://sensio-labs.com/',
            'affiliate[email]' => 'jobeet@example.com'
        ));

        $client->submit($form);
        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::createAction', $client->getRequest()->attributes->get('_controller'));

        $kernel = static::createKernel();
        $kernel->boot();
        $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

        $query = $em->createQuery('SELECT count(a.email) FROM MyBundle:Affiliate a WHERE a.email = :email');
        $query->setParameter('email', 'jobeet@example.com');
        $this->assertEquals(0, $query->getSingleScalarResult());

        $event->stop();
        echo "testAffilateForm : " . $event->getDuration() . " ms" . PHP_EOL;
    }

    public function testCreate()
    {
        $event = $this->stopwatch->start('testCreate');

        $client = static::createClient();
        $crawler = $client->request('GET', '/affiliate/new');
        $form = $crawler->selectButton('Submit')->form(array(
            'affiliate[url]' => 'http://sensio-labs.com/',
            'affiliate[email]' => 'address@example.com'
        ));

        $client->submit($form);
        $client->followRedirect();

        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::waitAction', $client->getRequest()->attributes->get('_controller'));

        $event->stop();
        echo "testCreate : " . $event->getDuration() . " ms" . PHP_EOL;

        return $client;
    }

    public function testWait()
    {
        $event = $this->stopwatch->start('testWait');

        $client = static::createClient();
        $crawler = $client->request('GET', '/affiliate/wait');

        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::waitAction', $client->getRequest()->attributes->get('_controller'));

        $event->stop();
        echo "testWait : " . $event->getDuration() . " ms" . PHP_EOL;
    }
}
