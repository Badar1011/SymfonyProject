<?php

namespace Book\ReviewBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GoogleApiController extends Controller
{
    public function booksAction()
    {
        return $this->render('BookReviewBundle:GoogleApi:books.html.twig', array(
            // ...
        ));
    }

    public function bookAction()
    {
        return $this->render('BookReviewBundle:GoogleApi:book.html.twig', array(
            // ...
        ));
    }

}
