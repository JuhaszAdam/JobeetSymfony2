<?php

namespace MyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Stopwatch\Stopwatch;

class CategoryControllerTest extends WebTestCase
{
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    public function testShow()
    {
        $this->stopwatch = new Stopwatch();
        $event = $this->stopwatch->start('testShow');

        $kernel = static::createKernel();
        $kernel->boot();
        $max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
        $max_jobs_on_category = $kernel->getContainer()->getParameter('max_jobs_on_category');

        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $link = $crawler->selectLink('Programming')->link();
        $crawler = $client->click($link);
        $this->assertEquals('MyBundle\Controller\CategoryController::showAction', $client->getRequest()->attributes->get('_controller'));
        $this->assertEquals('programming', $client->getRequest()->attributes->get('slug'));

        $this->assertRegExp('/page 1\/2/', $crawler->filter('.pagination_desc')->text());

        $link = $crawler->selectLink('2')->link();
        $crawler = $client->click($link);
        $this->assertEquals(2, $client->getRequest()->attributes->get('page'));
        $this->assertRegExp('/page 2\/2/', $crawler->filter('.pagination_desc')->text());

        $event->stop();
        echo "testShow : " . $event->getDuration() . " ms" . PHP_EOL;
    }
}
