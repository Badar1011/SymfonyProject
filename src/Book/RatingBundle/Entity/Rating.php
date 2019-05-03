<?php

namespace Book\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rating
 *
 * @ORM\Table(name="rating")
 * @ORM\Entity(repositoryClass="Book\RatingBundle\Repository\RatingRepository")
 */
class Rating
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="vote", type="boolean")
     */
    private $vote;

    /**
     * @var \Book\ReviewBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Book\ReviewBundle\Entity\User",inversedBy="rating")
     * @ORM\JoinColumn(name="ratedby", referencedColumnName="id")
     *
     */
    private $ratedby;


    /**
     * @var \Book\ReviewBundle\Entity\Review
     * @ORM\ManyToOne(targetEntity="Book\ReviewBundle\Entity\Review",inversedBy="rating")
     * @ORM\JoinColumn(name="reviewId", referencedColumnName="id")
     *
     */
    private $reviewId;



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
     * Set vote.
     *
     * @param bool $vote
     *
     * @return Rating
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote.
     *
     * @return bool
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set ratedby.
     *
     * @param \Book\ReviewBundle\Entity\User|null $ratedby
     *
     * @return Rating
     */
    public function setRatedby(\Book\ReviewBundle\Entity\User $ratedby = null)
    {
        $this->ratedby = $ratedby;

        return $this;
    }

    /**
     * Get ratedby.
     *
     * @return \Book\ReviewBundle\Entity\User|null
     */
    public function getRatedby()
    {
        return $this->ratedby;
    }

    /**
     * Set reviewId.
     *
     * @param \Book\ReviewBundle\Entity\Review|null $reviewId
     *
     * @return Rating
     */
    public function setReviewId(\Book\ReviewBundle\Entity\Review $reviewId = null)
    {
        $this->reviewId = $reviewId;

        return $this;
    }

    /**
     * Get reviewId.
     *
     * @return \Book\ReviewBundle\Entity\Review|null
     */
    public function getReviewId()
    {
        return $this->reviewId;
    }
}
