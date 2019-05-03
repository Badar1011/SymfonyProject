<?php

namespace Book\ReviewBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookReviewControllerTest extends WebTestCase
{
    public function testViewbook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/viewbook');
    }

    public function testCreatebook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/createbook');
    }

    public function testEditbook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/editbook');
    }

    public function testDeletebook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deletebook');
    }

}
