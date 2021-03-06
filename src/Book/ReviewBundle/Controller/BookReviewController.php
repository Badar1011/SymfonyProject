<?php

namespace Book\ReviewBundle\Controller;

use blackknight467\StarRatingBundle\Form\RatingType;
use Book\ReviewBundle\Entity\Book;
use Book\ReviewBundle\Entity\Rating;
use Book\ReviewBundle\Form\BookType;
use Book\ReviewBundle\Security\BookVoter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Book\ReviewBundle\Entity\Entry;
use Book\ReviewBundle\Form\EntryType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Book\ReviewBundle\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;


class BookReviewController extends Controller
{
    public function viewbookAction($id, Request $request)
    {
        // Get the doctrine Entity manager
        $em = $this->getDoctrine()->getManager();
        // Use the entity manager to retrieve the Book entity for the id
        // that has been passed
        $book = $em->getRepository('BookReviewBundle:Book')->find($id);

        // gets the reviews query
        $reviewsQuery = $em->getRepository('BookReviewBundle:Review')->getQueryForReviews($id);

        $paginator = $this->get('knp_paginator');

        $result = $paginator->paginate(
            $reviewsQuery,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',5)
        );

      //  return new Response(\Doctrine\Common\Util\Debug::dump([0]['id']));
      //  return new Response(\Doctrine\Common\Util\Debug::dump($em->getRepository('BookReviewBundle:Rating')->getLikes(72)));


        // Pass the Book entity to the view for displaying,
        return $this->render('BookReviewBundle:BookReview:viewbook.html.twig', array(
            'book' => $book, 'reviews' => $result,
        ));

    }

    public function createBookAction(Request $request)
    {
        // Create an new (empty) Book entity
        $book = new Book();
        $form = $this->createForm(BookType::class,$book,['action' => $request->getUri()]);
        $form->handleRequest($request);
        // I can delete isSubmitted
        // i can also test this by using getData() method

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
        /*    $fileName = $this->generateUniqueFileName().'.'.$image->guessExtension();
            // Move the file to the directory where images are stored
            $image->move($this->getParameter('image_directory'), $fileName);*/
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

    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }




    public function editbookAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('BookReviewBundle:Book')->find($id);
        if (!$this->isGranted(BookVoter::EDIT,$book))
        {
            $this->addFlash('danger','you can not edit this book');
            return $this->redirectToRoute('book_index');

//            throw new LogicException('you cannot perform this action');
        }
        $book->setImage(new File($this->getParameter('image_directory').'/'.$book->getImage()));
        $form = $this->createForm(BookType::class, $book, ['action' => $request->getUri()]);
        $form->handleRequest($request);
        if($form->isValid() and $form->isSubmitted()) {



            $fileUploader = new FileUploader($this->getParameter('image_directory'));

            $image = $book->getImage();
            /*    $fileName = $this->generateUniqueFileName().'.'.$image->guessExtension();
                // Move the file to the directory where images are stored
                $image->move($this->getParameter('image_directory'), $fileName);*/
            $fileName = $fileUploader->upload($image);

            $book->setImage($fileName);



            $em->flush();
            $this->addFlash('success','you have edited details of  ' . $book->getTitle());
            return $this->redirect($this->generateUrl('viewbook', ['id' => $book->getId()]));
        }

        return $this->render('BookReviewBundle:BookReview:editbook.html.twig', array(
            'form' => $form->createView(), 'book' => $book,
        ));
    }

    public function deletebookAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $book = $em->getRepository('BookReviewBundle:Book')->find($id);

        if (!$this->isGranted(BookVoter::DELETE,$book))
        {
            $this->addFlash('warning','you can not delete this book');
            return $this->redirectToRoute('book_index');
        }
        try {

            $image = $book->getImage();
            $em->remove($book);
            $em->flush();
            $path = $this->getParameter('image_directory').'/'.$image;
            $fileSystem = new Filesystem();
            $fileSystem->remove(array($path));
            $this->addFlash('success','you have successfully deleted  ' . $book->getTitle() . ' from the library');

        } catch (Exception $e)
        {

            $this->addFlash('danger','you have to delete the reviews before you can delete an image');
        }

        return $this->redirectToRoute('book_index');

    }



    public function booksbyauthorAction($name, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $authorBooksQuery = $em->getRepository('BookReviewBundle:Book')->getBooksForAnAuthor($name);

        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $authorBooksQuery,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',9)
        );


        return $this->render('BookReviewBundle:BookReview:booksbyauthor.html.twig', array(
            'books' => $result,
        ));
    }


    public function findbyauthorAction()
    {
        $em = $this->getDoctrine()->getManager();
        $bookSorted = $em->getRepository('BookReviewBundle:Book')->getBooksByAuthor();
        return $this->render('BookReviewBundle:BookReview:findbyauthor.html.twig', array(
            'books' => $bookSorted,
        ));
    }

}
