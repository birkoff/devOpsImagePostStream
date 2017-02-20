<?php


namespace AppBundle\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\File\File;
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
    private $localStorageService;

    private $cloudStorageService;

    /**
     * @var EventService $event
     */
    private $event;

    /**
     * PostService constructor.
     * PostRepository
     * @param ObjectRepository $repository
     * @param EventInterface|EventService $eventService
     * @param StorageInterface $localStorageService
     * @param StorageInterface $cloudStorageService
     * @internal param ObjectStorageHelper $storageHelper
     */
    public function __construct(ObjectRepository $repository, EventInterface $eventService, StorageInterface $localStorageService, StorageInterface $cloudStorageService)
    {
        $this->repository = $repository;
        $this->eventService = $eventService;
        $this->localStorageService = $localStorageService;
        $this->cloudStorageService = $cloudStorageService;
    }

    /**
     * @param int $page
     * @return Paginator
     */
    public function findBatch($page = 1)
    {
        $firstResult = $this->getFistResultStarts($page);
        $query = $this->repository->findBatch($firstResult);
        return new Paginator($query);
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
     * @param $title
     * @param $file
     * @return mixed
     * @internal param $imageUrl
     * @internal param $attributes
     * @internal param Post $post
     */
    public function create($title, $file)
    {
        $imageUrl = $this->handleUploadedFile($file);

        $post = new Post();
        $post->setTitle($title);
        $post->setImageUrl($imageUrl);

        $this->repository->create($post);

        $this->eventService->sendMessage('new_post');
    }

    /**
     * @return int
     */
    public function countPosts()
    {
        return $this->repository->countAllPosts();
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
        if(!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            throw new InvalidExtensionException($file->getMimeType());
        }

        if($file->getSize() > 2000000) { // MB
            throw new InvalidFileSizeException($file->getSize());
        }

        $filename = uniqid() . "." . $file->getClientOriginalExtension();
        $localFile = $this->localStorageService->save($file, $filename);
//        $localFile = new File($localFile);
        $fileUrl = $this->cloudStorageService->save($localFile, $filename);
        return $fileUrl;
    }
}