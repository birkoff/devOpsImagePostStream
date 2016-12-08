<?php


namespace AppBundle\Service;


use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Exceptions\InvalidExtensionException;
use AppBundle\Exceptions\InvalidFileSizeException;

class PostService
{
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
        return $this->repository->findBatch($firstResult);
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
        return $this->repository->countAllPosts();
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
        return ($page * PostRepository::MAX_RESULTS) - PostRepository::MAX_RESULTS;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws InvalidExtensionException
     * @throws InvalidFileSizeException
     */
    public function handleUploadedFile(UploadedFile $file)
    {
        if(!in_array($file->getMimeType(), ['image/jpeg', 'image/gif', 'image/png'])) {
            throw new InvalidExtensionException($file->getMimeType());
        }

        if($file->getSize() > 20000000) {
            throw new InvalidFileSizeException($file->getSize());
        }

        return $this->storageHelper->handleUpload($file);
    }
}