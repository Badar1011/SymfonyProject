<?php

namespace Book\RatingBundle\Repository;

use Symfony\Component\HttpFoundation\Response;

/**
 * RatingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RatingRepository extends \Doctrine\ORM\EntityRepository
{
    public function getLikes($reviewId)
    {
        $queryBuilder = $this->createQueryBuilder('rating');
        $queryBuilder->select('count(rating.vote)')
            ->where('rating.reviewId = :reviewId')
            ->andWhere('rating.vote = 1')
            ->setParameter('reviewId',$reviewId);

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

}
