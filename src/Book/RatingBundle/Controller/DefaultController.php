<?php

namespace Book\RatingBundle\Controller;

use Book\RatingBundle\Entity\Rating;
use Book\RatingBundle\Form\RatingType;
use Book\ReviewBundle\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request, Review $review)
    {
        $count =  array_filter( $review->getRating()->getValues(), function (Rating $rating){
            return $rating->getVote();
        });
        $like = sizeof($count);
        $num = sizeof($review->getRating()->getValues());
        $dislike = $num - $like;
        return $this->render('BookRatingBundle:Default:index.html.twig',  array(
            'review' => $review,  'like' => $like, 'dislike' => $dislike,
        ));
    }


    public function likeAction(Request $request, Review $review)
    {
        // Get the doctrine Entity manager
        $em = $this->getDoctrine()->getManager();
        $rating = new Rating();
        if (is_null($this->getUser()))
        {
         //   return $this->redirect($this->generateUrl('viewbook', ['id' => $review->getReviewof()->getId()]));
        }
        else
        {

            $answer = $em->getRepository('BookRatingBundle:Rating')->findOneBy(
                array('ratedby' => $this->getUser(), 'reviewId' => $review)
            );
            if (!empty($answer))
            {
                if (strcmp(($answer->getVote()) ? 'true' : 'false',"true") == 0)
                {
                    $this->addFlash('danger','you have already liked this review');

                    // return new Response("liked already");
                }
                else
                {
                    $answer->setVote(true);
                    $em->flush();
                    //  return new Response("going to change dislike  to like");
                }

            }
            else
            {

                $rating->setVote(true);
                $rating->setReviewId($review);
                $rating->setRatedby($this->getUser());
                $em->persist($rating);
                $em->flush();
            }
            return $this->redirect($this->generateUrl('viewbook', ['id' => $review->getReviewof()->getId()]));
        }

        $count =  array_filter( $review->getRating()->getValues(), function (Rating $rating){
            return $rating->getVote();
        });
        $like = sizeof($count);
        $num = sizeof($review->getRating()->getValues());
        $dislike = $num - $like;
        return $this->render('BookRatingBundle:Default:index.html.twig',  array(
            'review' => $review,  'like' => $like, 'dislike' => $dislike,
        ));
    }





    public function dislikeAction(Request $request, Review $review)
    {
        // Get the doctrine Entity manager
        $em = $this->getDoctrine()->getManager();
        $rating = new Rating();
        if (is_null($this->getUser()))
        {
          //  return $this->redirect($this->generateUrl('viewbook', ['id' => $review->getReviewof()->getId()]));
        }
        else
        {

            $answer = $em->getRepository('BookRatingBundle:Rating')->findOneBy(
                array('ratedby' => $this->getUser(), 'reviewId' => $review)
            );
            if (!empty($answer))
            {
                if (strcmp(($answer->getVote()) ? 'true' : 'false',"false") == 0)
                {
                    $this->addFlash('danger','you have already disliked this review');

                    // return new Response("liked already");
                }
                else
                {
                    $answer->setVote(false);
                    $em->flush();
                    //  return new Response("going to change dislike  to like");
                }

            }
            else
            {

                $rating->setVote(false);
                $rating->setReviewId($review);
                $rating->setRatedby($this->getUser());
                $em->persist($rating);
                $em->flush();


            }
            return $this->redirect($this->generateUrl('viewbook', ['id' => $review->getReviewof()->getId()]));
        }

        $count =  array_filter( $review->getRating()->getValues(), function (Rating $rating){
            return $rating->getVote();
        });
        $like = sizeof($count);
        $num = sizeof($review->getRating()->getValues());
        $dislike = $num - $like;

        return $this->render('BookRatingBundle:Default:index.html.twig',  array(
            'review' => $review, 'like' => $like, 'dislike' => $dislike,
        ));
    }

}
