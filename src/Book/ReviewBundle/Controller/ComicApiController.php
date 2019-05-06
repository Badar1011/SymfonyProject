<?php

namespace Book\ReviewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use GuzzleHttp\Client;

use Symfony\Component\HttpFoundation\Request;
class ComicApiController extends Controller
{

    protected $apiKey, $ts, $privateKey;

    public function __construct()
    {
        $this->ts = new \DateTime();
        $this->apiKey = $_ENV['MARVEL_COMIC_API_KEY'];
        $this->privateKey = $_ENV['MARVEL_COMIC_PRIVATE_KEY'];
    }

    public function oneComicBookAction($id)
    {
       $comic = $this->getOneComicBook($id);


        return $this->render('BookReviewBundle:ComicApi:one_comic_book.html.twig', array(
            'book' => $comic,
        ));
    }


    public function getOneComicBook($id)
    {
        $ts = $this->ts->getTimestamp();
        $hash = md5($ts . $this->privateKey . $this->apiKey);
        $client = new Client([
            'base_uri' => 'https://gateway.marvel.com:443/v1/public/comics/',
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $response = $client->request('GET', "$id?ts=$ts&apikey=$this->apiKey&hash=$hash");
        return json_decode($response->getBody()->getContents())->data->results;
    }


    public function comicBookPaginationAction($page)
    {
        $session = $this->get('session');
        $title = $session->get('searchcomic');
        if (strcmp($page,"next") == 0)
        {
            $newIndex = $session->get('comicIndex');
            $newIndex =$newIndex + 10;
            $session->set('comicIndex',$newIndex);
        }
        elseif (strcmp($page,"previous") == 0)
        {
            $newIndex = $session->get('comicIndex');
            if ($newIndex != 0)
            {
                $newIndex =$newIndex -10;
            }
            $session->set('comicIndex',$newIndex);
        }
        else
        {
            $newIndex = 0;
            $session->set('comicIndex',$newIndex);
        }
       $ts = $this->ts->getTimestamp();
       $hash = md5($ts . $this->privateKey . $this->apiKey);

        $client = new Client([
            'base_uri' => 'https://gateway.marvel.com:443/v1/public/',
            'headers' => ['Content-Type' => 'application/json']
        ]);
        $response = $client->request('GET', "comics?title=$title&ts=$ts&apikey=$this->apiKey&hash=$hash&offset=$newIndex&limit=12");
        $books = json_decode($response->getBody()->getContents())->data->results;

        return $this->render('BookReviewBundle:ComicApi:comic_books.html.twig', array(
            "comics" => $books,
        ));
    }






    public function comicBooksAction(Request $request)
    {
       $title = $request->request->get('comicsearch');
       $session = $this->get('session');
        if ($title != null)
        {
            $session->set('searchcomic',$title);
          //  var_dump("in first bit");die;
        }
        elseif ($session->get('searchcomic') != null) {
            $title = $session->get('searchcomic');
          //  var_dump("in sec bit");die;
        }
        else
        {
           $title = "avengers";
           $session->set('comicIndex', 0);
        }


        $ts = $this->ts->getTimestamp();
        $hash = md5($ts . $this->privateKey . $this->apiKey);

        $client = new Client([
            'base_uri' => 'https://gateway.marvel.com:443/v1/public/',
            'headers' => ['Content-Type' => 'application/json']
        ]);
      //  var_dump($title);
        $response = $client->request('GET', "comics?title=$title&ts=$ts&apikey=$this->apiKey&hash=$hash&offset=0&limit=12");
        $books = json_decode($response->getBody()->getContents())->data->results;

        return $this->render('BookReviewBundle:ComicApi:comic_books.html.twig', array(
            "comics" => $books,
        ));
    }

}
