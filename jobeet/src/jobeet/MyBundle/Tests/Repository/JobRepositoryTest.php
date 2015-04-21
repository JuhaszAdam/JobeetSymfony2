<?php

namespace jobeet\MyBundle\Tests\Repository;

use jobeet\MyBundle\Entity\Job;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;


class JobRepositoryTest extends WebTestCase
{
    private $em;
    private $application;

    /**
     * @throws \Exception
     */
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

    public function testGetForLuceneQuery()
    {
        $job = new Job();
        $job->setType('part-time');
        $job->setCompany('Sensio');
        $job->setPosition('FOO6');
        $job->setLocation('Paris');
        $job->setDescription('WebDevelopment');
        $job->setHowToApply('Send resumee');
        $job->setEmail('jobeet[at]example.com');
        $job->setUrl('http://sensio-labs.com');
        $job->setIsActivated(false);
        $job->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));

        $this->em->persist($job);
        $this->em->flush();

        $jobs = $this->em->getRepository('MyBundle:Job')->getForLuceneQuery('FOO6');
        $this->assertEquals(count($jobs), 0);

        $job = new Job();
        $job->setType('part-time');
        $job->setCompany('Sensio');
        $job->setPosition('FOO7');
        $job->setLocation('Paris');
        $job->setDescription('WebDevelopment');
        $job->setHowToApply('Send resumee');
        $job->setEmail('jobeet[at]example.com');
        $job->setUrl('http://sensio-labs.com');
        $job->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $job->setIsActivated(true);

        $this->em->persist($job);
        $this->em->flush();

        $jobs = $this->em->getRepository('MyBundle:Job')->getForLuceneQuery('position:FOO7');
        $this->assertEquals(count($jobs), 1);
        foreach ($jobs as $job_rep) {
            $this->assertEquals($job_rep->getId(), $job->getId());
        }

        $this->em->remove($job);
        $this->em->flush();

        $jobs = $this->em->getRepository('MyBundle:Job')->getForLuceneQuery('position:FOO7');

        $this->assertEquals(count($jobs), 0);
    }
}
