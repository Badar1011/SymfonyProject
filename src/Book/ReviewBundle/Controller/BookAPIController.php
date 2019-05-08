<?php
/**
 * Created by PhpStorm.
 * User: badar
 * Date: 11/04/19
 * Time: 15:00
 */

namespace Book\ReviewBundle\Controller;

use Book\RatingBundle\Entity\Rating;
use Book\RatingBundle\Form\RatingType;
use Book\ReviewBundle\Entity\Book;
use Book\ReviewBundle\Entity\Review;
use Book\ReviewBundle\Entity\User;
use Book\ReviewBundle\Form\BookType;
use Book\ReviewBundle\Form\ReviewType;
use Book\ReviewBundle\Service\FileUploader;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

class BookAPIController extends FOSRestController
{

    // its working, gets all book, /api/v1/books [GET]
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getBooksAction()
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('BookReviewBundle:Book')->findAll();
        return $this->handleView($this->view($books));
    }


    // its working, get 1 book, /api/v1/books/{id} [GET]
    public function getBookAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('BookReviewBundle:Book')->find($id);
        if(!$book) {
            // no book is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the book exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($book);
        }
        return $this->handleView($view);
    }


    // its working, deletes a book, /api/v1/books/{bookId} [DELETE]

    public function deleteBookAction($bookId)
    {
        if (!$this->getStaff())  {
            throw new AccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();


        $em = $this->getDoctrine()->getManager();
        /** @var Book $book */
        $book = $em->getRepository('BookReviewBundle:Book')->findOneBy(['id' => $bookId]);
        if(!$book) {
            // no blog entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            if ($book->getPublisher() === $user)
            {

                // the blog entry exists, so we pass it to the view
                // and the status code defaults to 200 "OK"
                $em->remove($book);
                $em->flush();
                $view = $this->view(null, 200);
            }
            else
            {
                $view = $this->view(null, 403);
            }
        }
        return $this->handleView($view);
    }








        //      /api/v1/books
    // its working, post a book, made books sub resource of user, pass user id to use it to make him owner of the book /api/v1/books [POST]
    public function postBooksAction(Request $request)
    {
        if (!$this->getStaff())  {
            throw new AccessDeniedException();
        }


        $user = $this->getUser();
        $book = new Book();
        // prepare the form
        $form = $this->createForm(BookType::class, $book);

        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }


      //  return $this->handleView($this->view($form->getErrors(), 400));

        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));

        $object = json_decode($request->getContent());
        // Point 2 of list above
        if($form->isValid()) {
            // Point 4 of list above
            $em = $this->getDoctrine()->getManager();
                $book->setPublisher($user);
                $book->setImage($object->image);
                $book->setTimestamp(new \DateTime());
                $em->persist($book);
                $em->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book', ['id' => $book->getId()]))
                );
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }




    // /api/v1/books/{id} [PUT]

    public function putBookAction(Request $request, $bookId)
    {

        if (!$this->getStaff())  {
            throw new AccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Book $book */
        $book = $entityManager->getRepository('BookReviewBundle:Book')->find($bookId);

        if (!$book)
        {
            return $this->handleView($this->view(null, 404));
        }

        $newBook = new Book();
        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        $form = $this->createForm(BookType::class, $newBook,
            ['action' => $request->getUri()]
        );

        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));


        // Point 2 of list above
        if($form->isValid()) {
            if ($book->getPublisher() === $user) {
                $book->setTitle($newBook->getTitle());
                $book->setBookauthor($newBook->getBookauthor());
                $book->setImage("1b8e78b815aac174cdcdf20538405056.jpeg");
                $book->setSummary($newBook->getSummary());
                // $newBook->setPublisher($book->getPublisher());
                // Point 4 of list above
                $entityManager->persist($book);
                $entityManager->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book', ['id' => $book->getId()]))
                );
            }
            else
            {
                $view = $this->view(null, 403);
                return $this->handleView($view);
            }
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }



// EVERYTHING WORKING UP TO THIS POINT


// {"title":"asda","bookauthor":"asda","summary":"A clash between good and evil awaits as young Harry (Daniel Radcliffe), Ron (Rupert Grint) and Hermione (Emma Watson) prepare for a final battle against Lord Voldemort (Ralph Fiennes). Harry has grown into a steely lad on a mission to rid the world of evil. The friends must search for the Horcruxes that keep the dastardly wizard immortal. Harry and Voldemort meet at Hogwarts Castle for an epic showdown where the forces of darkness may finally meet their match.","isbn":"12312312312","image":"4c14e954e405d7cb7c635a99b85a1d30.jpeg"}

///api/v1/users/books/81

        // IT WORKS GETS ALL REVIEWS FOR A BOOK
        //api/v1/books/{bookId}/reviews
    public function getBookReviewsAction($bookId)
    {
        $em = $this->getDoctrine()->getManager();
        $reviews = $em->getRepository('BookReviewBundle:Review')->findBy(['reviewof' => $bookId]);
        if(!$reviews) {
            // no book is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the book exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($reviews);
        }
        return $this->handleView($view);
    }


    // IT WORKS GETS ONE REVIEW OF A BOOK
    // api_book_get_book_review
    //  /api/v1/books/{bookId}/reviews/{reviewId}  [GET]
    public function getBookReviewAction($bookId, $reviewId)
    {
        $em = $this->getDoctrine()->getManager();
        //   $book = $em->getRepository('BookReviewBundle:Book')->find($bookId);
        $review = $em->getRepository('BookReviewBundle:Review')->findOneBy(['reviewof' => $bookId, 'id' => $reviewId]);
        if(!$review) {
            // no book is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the book exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($review);
        }
        return $this->handleView($view);
    }


    # TODO auto done for delete user book review and delete user book, also done for post book and post review, ALSO DONE FOR put review and post review


    // not sure if i need the book id as i can just use the review id to find the review
    // and use the user id to confirm that the correct person is deleting it
    // /api/v1/users/books/{bookId}/reviews/{reviewId}
    /**
     * DELETE Route annotation.
     * @REST\Delete("/users/books/{bookId}/reviews/{reviewId}")
     */
    public function deleteUserBookReviewAction($bookId,$reviewId)
    {
        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var Review $review */
        $review = $em->getRepository('BookReviewBundle:Review')->findOneBy(['reviewof' => $bookId, 'id' => $reviewId]);
        if(!$review) {
            // no blog entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        }
        else {

            if ($review->getReviewer() === $user)
            {
                // the blog entry exists, so we pass it to the view
                // and the status code defaults to 200 "OK"
                $em->remove($review);
                $em->flush();
                $view = $this->view(null, 200);
            }
            else
            {
                $view = $this->view(null, 403);
            }
        }
        return $this->handleView($view);
    }







    // it works

    //      /api/v1/users/books/{bookId}/reviews

    /**
     * POST Route annotation.
     * @REST\Post("/users/books/{bookId}/reviews")
     */
    public function postUserBookReviewsAction(Request $request, $bookId)
    {
        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }
        /** @var User $user */
        $user = $this->getUser();
        $review = new Review();
        // prepare the form
        $form = $this->createForm(ReviewType::class, $review);

        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }


        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));


        // Point 2 of list above
        if($form->isValid()) {
            // Point 4 of list above

            $em = $this->getDoctrine()->getManager();
          //  $user = $em->getRepository('BookReviewBundle:User')->find($userId);
            $book = $em->getRepository('BookReviewBundle:Book')->find($bookId);
            if (!$book)
            {
                return $this->handleView($this->view(null, 404));
            }
            if ($user instanceof User and $book instanceof Book)
            {
                $review->setReviewer($user);
                $review->setReviewof($book);
                $review->setTimestamp(new \DateTime());
                $em->persist($review);
                $em->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book_review', ['bookId' => $book->getId(), 'reviewId' => $review->getId()]))
                );
            }
            else
            {
                //  var_dump($user);
                return $this->handleView($this->view($form, 401));
            }
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }









    //   [PUT] /api/v1/users/books/{bookId}/reviews/{reviewId}

    /**
     * PUT Route annotation.
     * @REST\Put("/users/books/{bookId}/reviews/{reviewId}")
     */
    public function putUserBookReviewAction(Request $request, $bookId, $reviewId)
    {

        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }
        /** @var User $user */
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $review = $entityManager->getRepository('BookReviewBundle:Review')->findOneBy(['reviewof' => $bookId, 'id' => $reviewId]);
        if (!$review)
        {
            return $this->handleView($this->view(null, 404));
        }


        $newReview = new Review();
        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        $form = $this->createForm(ReviewType::class, $newReview,
            ['action' => $request->getUri()]
        );

        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));

        // Point 2 of list above
        if($form->isValid()) {
            if ($review->getReviewer() === $user)
            {
                /** @var Review $review */
                $review->setTitle($newReview->getTitle());
                $review->setArticle($newReview->getArticle());
                $review->setTimestamp(new \DateTime());

                // Point 4 of list above
                $entityManager->persist($review);
                $entityManager->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book_review', ['bookId' => $review->getReviewof(), 'reviewId' => $review->getId()])
                    ));
            }
            else
            {
                $view = $this->view(null, 403);
                return $this->handleView($view);
            }
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }



        //      /api/v1/books/reviews/{reviewId}/ratings

    /**
     * GET Route annotation.
     * @REST\Get("/books/reviews/{reviewId}/ratings")
     */
    public function getBookReviewRatingsAction($reviewId)
    {
        $em = $this->getDoctrine()->getManager();
        $reviews = $em->getRepository('BookRatingBundle:Rating')->findBy(['reviewId' => $reviewId]);
        if(!$reviews) {
            // no book is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the book exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($reviews);
        }
        return $this->handleView($view);
    }

        //      /api/v1/books/reviews/{reviewId}/ratings/{ratingId}
    /**
     * GET Route annotation.
     * @REST\Get("/books/reviews/{reviewId}/ratings/{ratingId}")
     */
    public function getBookReviewRatingAction($reviewId,$ratingId)
    {
        $em = $this->getDoctrine()->getManager();
        $reviews = $em->getRepository('BookRatingBundle:Rating')->findBy(['reviewId' => $reviewId, 'id' => $ratingId]);
        if(!$reviews) {
            // no book is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        } else {
            // the book exists, so we pass it to the view
            // and the status code defaults to 200 "OK"
            $view = $this->view($reviews);
        }
        return $this->handleView($view);
    }



    /**
     * POST Route annotation.
     * @REST\Post("/users/books/reviews/{reviewId}/ratings")
     */
    // /api/v1/users/books/reviews/{reviewId}/ratings
    public function postUserBookReviewRatingsAction(Request $request, $reviewId)
    {
        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }
        /** @var User $user */
        $user = $this->getUser();

        $rating = new Rating();
        // prepare the form
        $form = $this->createForm(RatingType::class, $rating);

        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        $em = $this->getDoctrine()->getManager();
        $review = $em->getRepository('BookReviewBundle:Review')->find($reviewId);
        if (!$review)
        {
            return $this->handleView($this->view(null, 404));
        }

        $alreadyExists = $em->getRepository('BookRatingBundle:Rating')->findOneBy(['reviewId' => $reviewId, 'ratedby' => $user->getId()]);
        if ($alreadyExists)
        {
            return $this->handleView($this->view(null, 409));
        }


        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));

        // Point 2 of list above
        if($form->isSubmitted()) {
                $rating->setRatedby($user);
                $rating->setReviewId($review);
                $v = json_decode($request->getContent(), true)['vote'];
                $rating->setVote($v);
                $em->persist($rating);
                $em->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book_review_rating', ['reviewId' => $reviewId, 'ratingId' => $rating->getId()]))
                );

        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }


    // /api/v1/users/books/reviews/{reviewId}/ratings/{ratingId}
    /**
     * DELETE Route annotation.
     * @REST\Delete("/users/books/reviews/{reviewId}/ratings/{ratingId}")
     */
    public function deleteUserBookReviewRatingAction($reviewId,$ratingId)
    {
        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var Rating $rating */
        $rating = $em->getRepository('BookRatingBundle:Rating')->findOneBy(['reviewId' => $reviewId, 'id' => $ratingId]);
        if(!$rating) {
            // no blog entry is found, so we set the view
            // to no content and set the status code to 404
            $view = $this->view(null, 404);
        }
        else {

            if ($rating->getRatedby() === $user)
            {
                // the blog entry exists, so we pass it to the view
                // and the status code defaults to 200 "OK"
                $em->remove($rating);
                $em->flush();
                $view = $this->view(null, 200);
            }
            else
            {
                $view = $this->view(null, 403);
            }
        }
        return $this->handleView($view);
    }


        //      /api/v1/users/books/reviews/{reviewId}/ratings/{ratingId}

    /**
     * PUT Route annotation.
     * @REST\Put("/users/books/reviews/{reviewId}/ratings/{ratingId}")
     */
    public function putUserBookReviewRatingAction(Request $request, $reviewId, $ratingId)
    {

        if (!$this->getUser())  {
            throw new AccessDeniedException();
        }
        /** @var User $user */
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        /** @var Rating $rating */
        $rating = $entityManager->getRepository('BookRatingBundle:Rating')->findOneBy(['reviewId' => $reviewId, 'id' => $ratingId]);
        if (!$rating)
        {
            return $this->handleView($this->view(null, 404));
        }



        // Point 1 of list above
        if($request->getContentType() != 'json') {
            return $this->handleView($this->view(null, 400));
        }
        $form = $this->createForm(RatingType::class, $rating,
            ['action' => $request->getUri()]
        );

        // json_decode the request content and pass it to the form
        $form->submit(json_decode($request->getContent(), true));

        // Point 2 of list above
        if($form->isSubmitted()) {
            if ($rating->getRatedby() === $user)
            {
                $v = json_decode($request->getContent(), true)['vote'];
                $rating->setVote($v);


                // Point 4 of list above
                $entityManager->persist($rating);
                $entityManager->flush();
                // set status code to 201 and set the Location header
                // to the URL to retrieve the blog entry - Point 5
                return $this->handleView($this->view(null, 201)
                    ->setLocation($this->generateUrl('api_book_get_book_review_rating', ['reviewId' => $reviewId, 'ratingId' => $rating->getId()]))
                );
            }
            else
            {
                $view = $this->view(null, 403);
                return $this->handleView($view);
            }
        } else {
            // the form isn't valid so return the form
            // along with a 400 status code
            return $this->handleView($this->view($form, 400));
        }
    }



    #TODO POST FUNCTIONS AND PUT AND DELETE FUNCTIONS FOR THE LIKE AND DISLIKE BUTTONS

}