<?php

namespace jobeet\MyBundle\Tests\Repository;

use Doctrine\ORM\EntityManager;
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
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var Application $application
     */
    private $application;

    public function setUp()
    {
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

    public function testGetForLuceneQuery()
    {
        $job = $this->createJob('FOO6');
        $job->setIsActivated(false);

        $this->em->persist($job);
        $this->em->flush();

        $jobs = $this->em->getRepository('MyBundle:Job')->getForLuceneQuery('FOO6');
        $this->assertEquals(count($jobs), 0);

        $job = $this->createJob('FOO7');
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

    /**
     * @param $position
     * @return Job
     */
    private function createJob($position)
    {
        $job = new Job();
        $job->setType('part-time');
        $job->setCompany('Sensio');
        $job->setPosition($position);
        $job->setLocation('Paris');
        $job->setDescription('WebDevelopment');
        $job->setHowToApply('Send resumee');
        $job->setEmail('jobeet[at]example.com');
        $job->setUrl('http://sensio-labs.com');
        $job->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));

        return $job;
    }
}
