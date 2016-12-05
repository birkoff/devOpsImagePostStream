<?php


namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostService
{
    const MAX_RESULTS = 15;

    /**
     * @var PostRepository $repository
     */
    private $repository;

    /**
     * @var ObjectStorageHelper $storageHelper
     */
    private $storageHelper;

    /**
     * PostService constructor.
     * PostRepository
     * @param ObjectRepository $repository
     * @internal param ObjectStorageHelper $storageHelper
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ObjectStorageHelper $storageHelper
     */
    public function setStorageHelper(ObjectStorageHelper $storageHelper)
    {
        $this->storageHelper = $storageHelper;
    }

    /**
     * @param int $page
     * @return array
     */
    public function findBatch($page = 1)
    {
        $firstResult = $this->getFistResultStarts($page);
        return $this->repository->createQueryBuilder('t')
            ->orderBy('t.id', 'DESC')
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

    public function getUploadUrl()
    {
        return $this->storageHelper->getUploadUrl();
    }

    /**
     * @param $page
     * @return int
     */
    private function getFistResultStarts($page)
    {
        return ($page * self::MAX_RESULTS) - self::MAX_RESULTS;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function handleUploadedFile(UploadedFile $file)
    {
        return $this->storageHelper->handleUpload($file);
    }
}