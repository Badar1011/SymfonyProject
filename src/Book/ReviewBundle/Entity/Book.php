<?php

namespace Book\ReviewBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
/**
 * Book
 *
 * @ORM\Table(name="book")
 * @ORM\Entity(repositoryClass="Book\ReviewBundle\Repository\BookRepository")
 * @UniqueEntity("isbn",errorPath="isbn",message="This book already exists")
 */
class Book
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="please enter the book title")
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="The title is too short",
     *     maxMessage="The title is too long."
     * )
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank(message="please enter the book author")
     * @Assert\Length(
     *     min=4,
     *     max=255,
     *     minMessage="The name is too short. put in full name",
     *     maxMessage="The name is too long."
     * )
     * @ORM\Column(name="bookauthor", type="string", length=255)
     */
    private $bookauthor;

    /**
     * @var string
     * @Assert\NotBlank(message="please write a summary of the book")
     * @Assert\Length(
     *     min=50,
     *     max=2000,
     *     minMessage="The summary is too short",
     *     maxMessage="The sumary is too long."
     * )
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;

    /**
     * @var string
     * @Assert\NotBlank(message="please enter the book isbn number")
     * @Assert\Length(
     *     min=10,
     *     max=21,
     *     minMessage="The number is too short",
     *     maxMessage="The number is too long."
     * )
     * @ORM\Column(name="isbn", type="string", length=255, unique=true)
     */
    private $isbn;

    /**
     * @var string
     * @Assert\Image()
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(name="timestamp", type="datetime")
     */
    private $timestamp;

    /**
     * @var \Book\ReviewBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Book\ReviewBundle\Entity\User",inversedBy="books")
     * @ORM\JoinColumn(name="publisher", referencedColumnName="id")
     *
     */
    private $publisher;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Book\ReviewBundle\Entity\Review",mappedBy="reviewof",cascade="remove")
     */
    protected $reviews;

    public function __construct()
    {
        $this->reviews = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timestamp = new \DateTime();
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

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Book
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set bookauthor.
     *
     * @param string $bookauthor
     *
     * @return Book
     */
    public function setBookauthor($bookauthor)
    {
        $this->bookauthor = $bookauthor;

        return $this;
    }

    /**
     * Get bookauthor.
     *
     * @return string
     */
    public function getBookauthor()
    {
        return $this->bookauthor;
    }

    /**
     * Set summary.
     *
     * @param string $summary
     *
     * @return Book
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary.
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set isbn.
     *
     * @param string $isbn
     *
     * @return Book
     */
    public function setIsbn($isbn)
    {
        $this->isbn = $isbn;

        return $this;
    }

    /**
     * Get isbn.
     *
     * @return string
     */
    public function getIsbn()
    {
        return $this->isbn;
    }

    /**
     * Set timestamp.
     *
     * @param \DateTime $timestamp
     *
     * @return Book
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Get timestamp.
     *
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set publisher.
     *
     * @param \Book\ReviewBundle\Entity\User|null $publisher
     *
     * @return Book
     */
    public function setPublisher(\Book\ReviewBundle\Entity\User $publisher = null)
    {
        $this->publisher = $publisher;

        return $this;
    }

    /**
     * Get publisher.
     *
     * @return \Book\ReviewBundle\Entity\User|null
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Add review.
     *
     * @param \Book\ReviewBundle\Entity\Review $review
     *
     * @return Book
     */
    public function addReview(\Book\ReviewBundle\Entity\Review $review)
    {
        $this->reviews[] = $review;

        return $this;
    }

    /**
     * Remove review.
     *
     * @param \Book\ReviewBundle\Entity\Review $review
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeReview(\Book\ReviewBundle\Entity\Review $review)
    {
        return $this->reviews->removeElement($review);
    }

    /**
     * Get reviews.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Book
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }


    public function __toString()
    {
        return (string) $this->getTitle();
    }
}
