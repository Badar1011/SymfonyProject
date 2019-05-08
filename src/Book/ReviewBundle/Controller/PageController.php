<?php

namespace Book\ReviewBundle\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{

    public function indexAction(Request $request)
    {


        $searchQuery = $request->get('query');
        print_r($searchQuery);

        if (!empty($searchQuery))
        {
          //  throw new \LogicException();
           // $finder = $this->container->get('fos_elastica.finder.app.book');
          //  $booksQuery = $finder->createPaginatorAdapter($searchQuery);
        }
        else
        {

            $em = $this->getDoctrine()->getManager();
            // gets the query for all the book order by time
            $booksQuery = $em->getRepository('BookReviewBundle:Book')->getLatestBookQuery();
        }


        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $booksQuery,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',9)
        );

        return $this->render('BookReviewBundle:Page:index.html.twig', array(
            'books' => $result,
        ));
    }

    public function aboutAction()
    {
        return $this->render('BookReviewBundle:Page:about.html.twig');
    }

    public function contactAction()
    {
        return $this->render('BookReviewBundle:Page:contact.html.twig');
    }



    public function showdetailsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $existingClient = $em->getRepository("BookReviewBundle:Client")->findOneBy(
            ["user" => $this->getUser()]
        );
        $clientId = null;
        $clientSecret = null;

        if (!$existingClient) {
            $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
            $client = $clientManager->createClient();
            $client->setRedirectUris(array('http://localhost'));
            $client->setAllowedGrantTypes(array('password', 'refresh_token', 'token', 'authorization_code'));
            $client->setUser($this->getUser());
            $clientManager->updateClient($client);
            $clientId = $client->getPublicId();
            $clientSecret = $client->getSecret();
        } else {
            $clientId = $existingClient->getPublicId();
            $clientSecret = $existingClient->getSecret();
        }

        return $this->render('BookReviewBundle:Page:generate.html.twig', array(
           'clientId' => $clientId, 'clientSecret' => $clientSecret, 'username' => $this->getUser()->getUsername(),
        ));
    }



}
