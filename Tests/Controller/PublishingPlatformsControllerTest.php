<?php

namespace Newscoop\PublishingPlatformsPluginBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PublishingPlatformsControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/publishingplatforms/Tester');

        $this->assertTrue($crawler->filter('html:contains("Hello Tester")')->count() > 0);
    }
}
