<?php

namespace Book\ReviewBundle\Controller;

use Book\ReviewBundle\Entity\Book;
use Book\ReviewBundle\Form\BookType;
use Book\ReviewBundle\Service\FileUploader;
use GuzzleHttp\Client;
use Knp\Bundle\PaginatorBundle\KnpPaginatorBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GoogleApiController extends Controller
{
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
        $book = $this->getOneBook($bookId);
        return $this->render('BookReviewBundle:GoogleApi:book.html.twig', array(
            "book" => $book,
        ));
    }


    public function addingBookAction(Request $request, $id)
    {

        $book = new Book();

        $googleBook = $this->getOneBook($id);

      //  var_dump($googleBook->volumeInfo->imageLinks->medium);
        $book->setTitle($googleBook->volumeInfo->title);
        $book->setBookauthor(join(',',$googleBook->volumeInfo->authors));
        $book->setSummary($googleBook->volumeInfo->description);
        var_dump($googleBook->volumeInfo->imageLinks->medium);
     //   $book->setImage($googleBook->volumeInfo->imageLinks->medium);
        $book->setIsbn($googleBook->volumeInfo->industryIdentifiers[0]->identifier);
       // $book->setIsbn(join(',',$googleBook->volumeInfo->authors));

    //    var_dump($googleBook->volumeInfo->industryIdentifiers[0]);
        $form = $this->createForm(BookType::class,$book,['action' => $request->getUri()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid())
        {
            // Retrieve the doctrine entity manager
            $em = $this->getDoctrine()->getManager();
            // manually set the author to the current user
            $book->setPublisher($this->getUser());
            // manually set the timestamp to a new DateTime object
            $book->setTimestamp(new \DateTime());



            $fileUploader = new FileUploader($this->getParameter('image_directory'));


            $image = $book->getImage();
            $fileName = $fileUploader->upload($image);

            $book->setImage($fileName);





            // tell the entity manager we want to persist this entity
            $em->persist($book);
            // commit all changes
            $em->flush();
            // shows the flash
            $this->addFlash('success','you have added ' . $book->getTitle() . ' book to the library');
            return $this->redirect($this->generateUrl('viewbook', ['id' => $book->getId()]));
            //return $this->redirectToRoute('book_index');
        }
        return $this->render('BookReviewBundle:BookReview:createbook.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function getOneBook($bookId)
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
        return $book;
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
