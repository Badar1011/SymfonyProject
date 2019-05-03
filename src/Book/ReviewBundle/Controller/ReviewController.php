<?php

namespace Book\ReviewBundle\Controller;

use Book\ReviewBundle\Entity\Book;
use Book\ReviewBundle\Entity\Review;
use Book\ReviewBundle\Form\ReviewType;
use Book\ReviewBundle\Security\BookVoter;
use Book\ReviewBundle\Security\ReviewVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Exception\LogicException;

class ReviewController extends Controller
{
    public function createreviewAction($id, Request $request)
    {

        // Create an new (empty) Review entity
        $review = new Review();
        $form = $this->createForm(ReviewType::class,$review,['action' => $request->getUri()]);
        $form->handleRequest($request);
        // I can delete isSubmitted
        // i can also test this by using getData() method

        if ($form->isSubmitted() and $form->isValid())
        {
            // Retrieve the doctrine entity manager
            $em = $this->getDoctrine()->getManager();
            $book = $em->getRepository('BookReviewBundle:Book')->find($id);
            // manually set the author to the current user
            $review->setReviewer($this->getUser());
            // manually set the book id to the setReview of to show the reviews of this book.
            $review->setReviewof($book);
            // manually set the timestamp to a new DateTime object
            $review->setTimestamp(new \DateTime());
            // tell the entity manager we want to persist this entity
            $em->persist($review);
            // commit all changes
            $em->flush();
            $this->addFlash('success','you have added a review.');
            return $this->redirect($this->generateUrl('viewbook', ['id' => $id]));
            //return $this->redirectToRoute('book_index');
        }

        return $this->render('BookReviewBundle:Review:createreview.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function editreviewAction($id, Request $request)
    {


        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('BookReviewBundle:Review')->find($id);
        if (!$this->isGranted(ReviewVoter::EDIT,$review))
        {
            $this->addFlash('warning','you can not edit this book');
            return $this->redirectToRoute('book_index');

          //  throw new LogicException('you cannot perform this action');
        }
        $form = $this->createForm(ReviewType::class, $review, ['action' => $request->getUri()]);
        $form->handleRequest($request);
        if($form->isValid() and $form->isSubmitted()) {
            $em->flush();
            $this->addFlash('success','you have successfully edited your review.');
            return $this->redirect($this->generateUrl('viewbook', ['id' => $review->getReviewof()->getId()]));
        }


        return $this->render('BookReviewBundle:Review:editreview.html.twig', array(
            'form' => $form->createView(), 'review' => $review,
        ));
    }

    public function deletereviewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('BookReviewBundle:Review')->find($id);

        if (!$this->isGranted(ReviewVoter::DELETE,$review))
        {
            $this->addFlash('warning','you can not delete this review');
            return $this->redirectToRoute('book_index');

        //    throw new LogicException('you cannot perform this action');
        }
        $em->remove($review);
        $em->flush();
        $this->addFlash('success','you have successfully deleted your review.');
        return $this->redirectToRoute('book_index');

    }

}
