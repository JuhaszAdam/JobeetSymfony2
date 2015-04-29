<?php

namespace MyBundle\Tests\Provider;

use MyBundle\Entity\Category;
use MyBundle\Entity\Job;
use MyBundle\Manager\Manager;
use MyBundle\Provider\Provider;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
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
    private function mockDatabaseManager()
    {
        return $this
            ->getMockBuilder('MyBundle\Manager\Manager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testProvideDataFromDatabase()
    {
        $genericJobs = iterator_to_array($this->getGenericJobs());

        $databaseManager = $this->mockDatabaseManager();

        $databaseManager->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($genericJobs));

        /** @var Manager $databaseManager */
        $dataProvider = new Provider($databaseManager);
        $jobs = iterator_to_array($dataProvider->provide());

        /** @var job[] $jobs */
        /** @var job[] $genericJobs */
        foreach ($jobs as $i => $returnedJob) {
            $this->assertEquals($returnedJob->getType(), $genericJobs[$i]->getType());
            $this->assertEquals($returnedJob->getCompany(), $genericJobs[$i]->getCompany());
            $this->assertEquals($returnedJob->getPosition(), $genericJobs[$i]->getPosition());
        }
    }
}
