<?php

namespace MyBundle\Tests\Manager;

use MyBundle\CacheDriver\CacheDriverRedis;
use MyBundle\Entity\Category;
use MyBundle\Entity\Job;
use MyBundle\Manager\Manager;
use MyBundle\Repository\JobRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

class DatabaseManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @return \Redis
     */
    private function getRedis()
    {
        if ($this->redis === null) {
            if (!class_exists(\Redis::class)) {
                $this->markTestSkipped("Redis isn't set up properly, skipping this test.");
            }
            $redis = new \Redis();

            $this->redis = $redis;
        }

        return $this->redis;
    }

    /**
     * @return \Traversable
     */
    private function getGenericJobs()
    {
        $genericCategory = new Category();
        $genericCategory->setName('Generic Category');

        for ($i = 0; $i < 10; $i++) {
            $job = new Job();
            $job->setCategory($genericCategory);
            $job->setType('full-time');
            $job->setCompany('Company ' . $i);
            $job->setPosition('Web Developer');
            $job->setLocation('Paris, France');
            $job->setDescription('Lorem ipsum dolor sit amet, consectetur adipisicing elit.');
            $job->setHowToApply('Send your resume to lorem.ipsum [at] dolor.sit');
            $job->setIsPublic(true);
            $job->setIsActivated(true);
            $job->setToken('job_' . $i);
            $job->setEmail('job@example.com');
            $job->setCreatedAt(new \DateTime());

            yield $i => $job;
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedRepository()
    {
        return $this
            ->getMockBuilder(JobRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedEntityManager()
    {
        return $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockedConnection()
    {
        return $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testFindFromDatabaseReturnsSpecificArray()
    {
        $genericJobs = iterator_to_array($this->getGenericJobs());
        $expectedJobs = $genericJobs;

        $jobRepository = $this->getMockedRepository();
        $entityManager = $this->getMockedEntityManager();

        $jobRepository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($genericJobs));

        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($jobRepository));

        /** @var EntityManager $entityManager */
        $databaseManager = new Manager($entityManager,
            new CacheDriverRedis($this->getRedis(), ['host' => 'localhost', 'port' => '11211']),
            Job::class,
            JobRepository::class);

        $findFromDatabaseResults = $databaseManager->findFromDatabase();
        for ($i = 0; $i < count($expectedJobs); $i++) {
            $this->assertEquals($expectedJobs[$i], $findFromDatabaseResults[$i]);
        }
    }

    public function testPushToDatabaseMethod()
    {
        $entityManager = $this->getMockedEntityManager();
        $connection = $this->getMockedConnection();

        $entityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));
        $entityManager->expects($this->exactly(10))
            ->method('persist');
        $entityManager->expects($this->exactly(4))
            ->method('flush');

        /** @var EntityManager $entityManager */
        $manager = new Manager($entityManager,
            new CacheDriverRedis($this->getRedis(), ['host' => 'localhost', 'port' => '11211']),
            Job::class,
            JobRepository::class);
        $manager->setFlushInterval(3);
        $jobs = iterator_to_array($this->getGenericJobs());
        try {
            $manager->pushToDatabase($jobs);
        } catch (\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    public function testPushToDatabaseMethodThrowsException()
    {
        $this->setExpectedException("Exception");
        $connection = $this->getMockedConnection();
        $entityManager = $this->getMockedEntityManager();

        $connection->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \Exception("Exception")));

        $entityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        /** @var EntityManager $entityManager */
        $manager = new Manager($entityManager,
            new CacheDriverRedis($this->getRedis(), ['host' => 'localhost', 'port' => '11211']),
            Job::class,
            JobRepository::class);
        $jobs = iterator_to_array($this->getGenericJobs());
        $manager->pushToDatabase($jobs);
    }

    public function testGettingAndSettingFlushInterval()
    {
        $entityManager = $this->getMockedEntityManager();
        /** @var EntityManager $entityManager */
        $manager = new Manager($entityManager,
            new CacheDriverRedis($this->getRedis(), ['host' => 'localhost', 'port' => '11211']),
            Job::class,
            JobRepository::class);

        $manager->setFlushInterval(5);
        $this->assertEquals(5, $manager->getFlushInterval());
        $manager->setFlushInterval(10);
        $this->assertEquals(10, $manager->getFlushInterval());
    }
}
