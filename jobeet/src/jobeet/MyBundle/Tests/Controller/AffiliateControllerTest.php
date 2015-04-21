<?php

namespace jobeet\MyBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;

class AffiliateControllerTest extends WebTestCase
{
    private $em;
    private $application;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->application = new Application(static::$kernel);

        // drop the database
        $command = new DropDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:drop',
            '--force' => true
        ));
        $command->run($input, new NullOutput());

        // we have to close the connection after dropping the database so we don't get "No database selected" error
        $connection = $this->application->getKernel()->getContainer()->get('doctrine')->getConnection();
        if ($connection->isConnected()) {
            $connection->close();
        }

        // create the database
        $command = new CreateDatabaseDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:database:create',
        ));
        $command->run($input, new NullOutput());

        // create schema
        $command = new CreateSchemaDoctrineCommand();
        $this->application->add($command);
        $input = new ArrayInput(array(
            'command' => 'doctrine:schema:create',
        ));
        $command->run($input, new NullOutput());

        // get the Entity Manager
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // load fixtures
        $client = static::createClient();
        $loader = new ContainerAwareLoader($client->getContainer());
        $loader->loadFromDirectory(static::$kernel->locateResource('@MyBundle/DataFixtures/ORM'));
        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testAffiliateForm()
    {
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
    }

    public function testCreate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/affiliate/new');
        $form = $crawler->selectButton('Submit')->form(array(
            'affiliate[url]' => 'http://sensio-labs.com/',
            'affiliate[email]' => 'address@example.com'
        ));

        $client->submit($form);
        $client->followRedirect();

        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::waitAction', $client->getRequest()->attributes->get('_controller'));

        return $client;
    }

    public function testWait()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/affiliate/wait');

        $this->assertEquals('jobeet\MyBundle\Controller\AffiliateController::waitAction', $client->getRequest()->attributes->get('_controller'));
    }
}
