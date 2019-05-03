<?php

namespace Book\ReviewBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewControllerTest extends WebTestCase
{
    public function testCreatereview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/createreview');
    }

    public function testViewreview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewreview');
    }

    public function testEditreview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/editreview');
    }

    public function testDeletereview()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deletereview');
    }

}
