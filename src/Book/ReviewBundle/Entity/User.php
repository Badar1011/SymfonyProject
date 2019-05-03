<?php

namespace Book\ReviewBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="WorkshopUsers")
 * @ORM\Entity(repositoryClass="Book\ReviewBundle\Repository\UserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="fullname", type="string", length=100)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile", "EditProfile"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $fullname;



    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Book\ReviewBundle\Entity\Book",mappedBy="publisher",cascade="remove")
     */
    protected $books;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Book\ReviewBundle\Entity\Review",mappedBy="reviewer",cascade="remove")
     */
    protected $articles;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Book\RatingBundle\Entity\Rating",mappedBy="ratedby",cascade="remove")
     */
    protected $rating;




    public function __construct()
    {
        parent::__construct();
        $this->books = new \Doctrine\Common\Collections\ArrayCollection();
        $this->articles = new \Doctrine\Common\Collections\ArrayCollection();
    }



    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->setUsername($email);
        return parent::setEmail($email); // TODO: Change the autogenerated stub
    }



    /**
     * Add book.
     *
     * @param \Book\ReviewBundle\Entity\Book $book
     *
     * @return User
     */
    public function addBook(\Book\ReviewBundle\Entity\Book $book)
    {
        $this->books[] = $book;

        return $this;
    }

    /**
     * Remove book.
     *
     * @param \Book\ReviewBundle\Entity\Book $book
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBook(\Book\ReviewBundle\Entity\Book $book)
    {
        return $this->books->removeElement($book);
    }

    /**
     * Get books.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * Add article.
     *
     * @param \Book\ReviewBundle\Entity\Review $article
     *
     * @return User
     */
    public function addArticle(\Book\ReviewBundle\Entity\Review $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * Remove article.
     *
     * @param \Book\ReviewBundle\Entity\Review $article
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeArticle(\Book\ReviewBundle\Entity\Review $article)
    {
        return $this->articles->removeElement($article);
    }

    /**
     * Get articles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getArticles()
    {
        return $this->articles;
    }


    public function __toString()
    {
        return parent::getEmail(); // TODO: Change the autogenerated stub

    }

    /**
     * Add rating.
     *
     * @param \Book\RatingBundle\Entity\Rating $rating
     *
     * @return User
     */
    public function addRating(\Book\RatingBundle\Entity\Rating $rating)
    {
        $this->rating[] = $rating;

        return $this;
    }

    /**
     * Remove rating.
     *
     * @param \Book\RatingBundle\Entity\Rating $rating
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeRating(\Book\RatingBundle\Entity\Rating $rating)
    {
        return $this->rating->removeElement($rating);
    }

    /**
     * Get rating.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRating()
    {
        return $this->rating;
    }
}
