<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Post;
use Doctrine\ORM\EntityRepository;

/**
 * PostRepository
 */
class PostRepository extends EntityRepository
{
    /**
     * @param Post $post
     */
    public function create(Post $post)
    {
        $em = $this->getEntityManager();
        $em->persist($post);
        $em->flush();
    }
}
