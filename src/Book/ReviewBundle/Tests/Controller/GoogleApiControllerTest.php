<?php

namespace Book\ReviewBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GoogleApiControllerTest extends WebTestCase
{
    public function testBooks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/books');
    }

    public function testBook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/book');
    }

}
