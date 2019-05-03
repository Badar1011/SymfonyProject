<?php

namespace Book\ReviewBundle\Controller;

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

}
