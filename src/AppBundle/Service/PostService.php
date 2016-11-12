<?php


namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectRepository;

class PostService
{
    const MAX_RESULTS = 5;

    /**
     * @var PostRepository $repository
     */
    private $repository;

    /**
     * PostService constructor.
     * PostRepository
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param int $page
     * @return array
     */
    public function findBatch($page = 1)
    {
        $firstResult = $this->getFistResultStarts($page);
        return $this->repository->createQueryBuilder('t')
            ->setMaxResults(self::MAX_RESULTS)
            ->setFirstResult($firstResult)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param Post $post
     * @return mixed
     */
    public function create(Post $post)
    {
        return $this->repository->create($post);
    }

    /**
     * @return int
     */
    public function countPosts()
    {
        return $this->repository->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param $page
     * @return int
     */
    private function getFistResultStarts($page)
    {
        return ($page * self::MAX_RESULTS) - self::MAX_RESULTS;
    }
}