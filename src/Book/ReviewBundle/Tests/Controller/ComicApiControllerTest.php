<?php

namespace Book\ReviewBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ComicApiControllerTest extends WebTestCase
{
    public function testOnecomicbook()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/oneComicBook');
    }

    public function testComicbooks()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/comicBooks');
    }

}
