<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Service\PaginationService;
use AppBundle\Service\PostService;
use AppBundle\Repository\PostRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use \Firebase\JWT\JWT;

class PostController extends Controller implements TokenAuthenticatedControllerInterface
{
    /**
     * @Route("/api/posts/{page}", name="post_list", requirements={"page": "\d+"})
     */
    public function listAction($page = 1)
    {
        /** @var PostService $postService */
        $postService = $this->get('app.post_service');

        $posts = $postService->findBatch($page);

        /** @var PaginationService $paginationService */
        $paginationService = $this->get('app.pagination_service');
        $paginationService->setPage($page)->setMaxResults(PostService::MAX_RESULTS)->setTotalItems($postService->countPosts());

        $postsArray = $this->getEntityAttributtes($posts);
        $responseArray = ['posts' => $postsArray, 'pagination'=> $paginationService->getPaginationValues()];
        $response = $this->json($responseArray);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', 'http://hector.dev');
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

        $serializer = $this->get('serializer');
        $jsonContent = $serializer->serialize($post, 'json');

        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', 'http://hector.dev');
        return $response;
    }

    /**
     * Matches /api/posts/stats exactly
     * @Route("/apiposts/stats")
     */
    public function statsAction()
    {
        $number = mt_rand(0, 100);
        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * Matches /api/post/new exactly
     * @Route("/api/post/new")
     */
    public function newAction(Request $request)
    {
        if($request->isMethod('GET')) {
            $s3 = $this->get('app.aws_s3');
            $uploadUrl = $s3->getUploadUrl();
            return $this->json(['upload_url' => $uploadUrl]);
        }

        $title = $request->request->get('title');
        $imageUrl = $request->request->get('imageUrl');

        $post = new Post();
        $post->setTitle($title)->setImageUrl($imageUrl);

        /** @var PostService $postService */
        $postService = $this->get('app.post_service');
        $postService->create($post);

        $response = $this->json(['message' => 'success', 'data' => ['title' => $title, 'url' => $imageUrl]]);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', 'http://hector.dev');
        return $response;
    }

    /**
     * Matches /api/posts/export exactly
     * @Route("/api/posts/export")
     */
    public function exportAction()
    {
        return $this->json(['status' => 'in_process']);

    }

    /**
     * Matches /api/posts/views exactly
     * @Route("/api/posts/views")
     */
    public function viewsAction()
    {
        $response = $this->json(['views' => '2045']);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', 'http://hector.dev');
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
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', 'http://hector.dev');
        return $response;
    }

    /**
     * @param $posts
     * @return array
     */
    private function getEntityAttributtes($posts)
    {
        $serializer = $this->get('serializer');
        $jsonContent = $serializer->serialize($posts, 'json');
        return json_decode($jsonContent, true);
    }
}