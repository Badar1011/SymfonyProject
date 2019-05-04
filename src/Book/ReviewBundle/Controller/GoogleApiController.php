<?php

namespace Book\ReviewBundle\Controller;

use GuzzleHttp\Client;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GoogleApiController extends Controller
{
    private $startIndex = 0;
    private $searchResult;
    public function booksAction(Request $request)
    {

       if ($request->request->get('search') == null)
       {

       }
       else
       {

           $searchResult = $request->request->get('search');
           $session = $this->get('session');
           $session->set('search', $searchResult);
           $session->set('index', 0);
       }


       $client = new Client([
            'base_uri' => 'https://www.googleapis.com/books/v1/',
            'headers' => [
                'apiKey' => 'AIzaSyB5EeEzqJceEWyd_FfwFpdzvzGi4PSbuQU',
                'Content-Type' => 'application/json'
            ]
        ]);
        $response = $client->request('GET', "volumes?q=$searchResult&maxResults=12&startIndex=0");



        $bookResults = json_decode($response->getBody()->getContents(), true)["items"];
       // $this->startIndex = $this->startIndex + 10;
       // var_dump($bookResults);
       // die;


   //     $paginator = $this->get('knp_paginator');
     //   $paginator->setParam('section', 'supplier');

     //   $result = $paginator->paginate($bookResults, 9);

               //  $this->paginationService->createPaginator($books, 6, $request);


                return $this->render('BookReviewBundle:GoogleApi:books.html.twig', array(
                "books" => $bookResults,
                ));
    }

    public function bookAction($bookId)
    {

        $client = new Client([
            'base_uri' => 'https://www.googleapis.com/books/v1/',
            'headers' => [
                'apiKey' => 'AIzaSyBFIAoLIIoQWuS4YOmaZldOaZSdvWU4skY',
                'Content-Type' => 'application/json'
            ]
        ]);
        $response = $client->request('GET', "volumes/$bookId");
        $book = json_decode($response->getBody()->getContents());
        return $this->render('BookReviewBundle:GoogleApi:book.html.twig', array(
            "book" => $book,
        ));
    }

    public function bookPaginationAction($page)
    {
       $session = $this->get('session');
       $q = $session->get('search');
var_dump($q);
        if (strcmp($page,"next") == 0)
        {
            $newIndex = $session->get('index');
            $newIndex =$newIndex + 10;
            $session->set('index',$newIndex);
        }
        elseif (strcmp($page,"previous") == 0)
        {
            $newIndex = $session->get('index');
            if ($newIndex == 0)
            {
                //
            }
            else
            {
                $newIndex =$newIndex -10;
            }

            $session->set('index',$newIndex);
        }
        else
        {
            $newIndex = 0;
            $session->set('index',$newIndex);
        }

        $client = new Client([
            'base_uri' => 'https://www.googleapis.com/books/v1/',
            'headers' => [
                'apiKey' => 'AIzaSyB5EeEzqJceEWyd_FfwFpdzvzGi4PSbuQU',
                'Content-Type' => 'application/json'
            ]
        ]);
        $response = $client->request('GET', "volumes?q=$q&maxResults=12&startIndex=$newIndex");



        $bookResults = json_decode($response->getBody()->getContents(), true)["items"];
        return $this->render('BookReviewBundle:GoogleApi:books.html.twig', array(
            "books" => $bookResults,
        ));
    }
}
