<?php

namespace AppBundle\Controller;

use AppBundle\Exceptions\InvalidExtensionException;
use AppBundle\Exceptions\InvalidFileSizeException;
use AppBundle\Service\PostService;
use AppBundle\Repository\PostRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

// docker run --name mysql-server -e MYSQL_ROOT_PASSWORD=root -d mysql/mysql-server

class PostController extends Controller implements TokenAuthenticatedControllerInterface
{
    /**
     * @Route("/api/posts/{page}", name="post_list", requirements={"page": "\d+"})
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function listAction($page = 1)
    {
        /** @var PostService $postService */
        $postService = $this->get('app.post_service');
        $posts = $postService->findBatch($page);

        /** @var PaginationService $paginationService */
        $paginationService = $this->get('app.pagination_service');
        $paginationService->setPage($page)
            ->setMaxResults(PostRepository::MAX_RESULTS)
            ->setTotalItems($postService->countPosts());

        $postsArray = $this->getEntityAttributes($posts);
        $responseArray = ['posts' => $postsArray, 'pagination'=> $paginationService->getPaginationValues()];
        $response = $this->json($responseArray);

        $response = $this->setResponseHeaders($response);
        return $response;
    }

    /**
     * @param $id
     * @return Response
     * @Route("/api/post/{id}", name="post_show", requirements={"id": "\d+"})
     */
    public function showAction($id)
    {
        /** @var PostService $postService */
        $postService = $this->get('app.post_service');

        /** @var \AppBundle\Entity\Post $post */
        $post = $postService->find($id);
        $jsonContent = $this->getEntityAttributes($post);

        $response = new Response($jsonContent);
        $response = $this->setResponseHeaders($response);
        return $response;
    }

    /**
     * Matches /api/post/create exactly
     * @Route("/api/post/create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $title = $request->request->get('title');

        /** @var  UploadedFile $file */
        $file = $request->files->get('upload');

        /** @var PostService $postService */
        $postService = $this->get('app.post_service');

        try {
            $postService->create($title, $file);
        } catch (InvalidExtensionException $e) {
            $response = $this->json(['error' => 'Invalid File extension ' . $e->getMessage()], 400);
            $response = $this->setResponseHeaders($response);
            return $response;
        } catch (InvalidFileSizeException $e) {
            $response = $this->json(['error' => 'Invalid File Size' . $e->getMessage()], 400);
            $response = $this->setResponseHeaders($response);
            return $response;
        }

        $jsonContent = $this->getEntityAttributes([]);
        $response = $this->json(['message' => 'success', 'data' => $jsonContent]);
        $response = $this->setResponseHeaders($response);
        return $response;
    }

    /**
     * Matches /api/posts/views exactly
     * @Route("/api/posts/views")
     */
    public function viewsAction()
    {
        $response = $this->json(['views' => '2045']);
        $response = $this->setResponseHeaders($response);
        return $response;
    }

    /**
     * Matches /api/posts/count exactly
     * @Route("/api/posts/count")
     */
    public function countAction()
    {
        /** @var PostService $postService */
        $postService = $this->get('app.post_service');
        $postCount = $postService->countPosts();
        $response = $this->json(['posts' => $postCount]);
        $response = $this->setResponseHeaders($response);
        return $response;
    }

    /**
     * @param $posts
     * @return array
     */
    private function getEntityAttributes($posts)
    {
        $serializer = $this->get('serializer');
        $jsonContent = $serializer->serialize($posts, 'json');
        return json_decode($jsonContent, true);
    }

    /**
     * @param $response
     */
    private function setResponseHeaders($response)
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}