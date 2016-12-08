<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 */
class PostRepository extends EntityRepository
{
    const MAX_RESULTS = 15;

    /**
     * @param $firstResult
     * @return array
     */
    public function findBatch($firstResult)
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.created', 'DESC')
            ->setMaxResults(self::MAX_RESULTS)
            ->setFirstResult($firstResult)
            ->getQuery()
            ->getArrayResult();
    }
    /**
     * @param Post $post
     */
    public function create(Post $post)
    {
        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();
    }

    /**
     * @return int
     */
    public function countAllPosts()
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
