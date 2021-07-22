<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Posts;

use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Http\Auth\AuthInterface;
use GeekBrains\Blog\Http\Auth\NotAuthenticatedException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MyPosts
 * @package GeekBrains\Blog\Http
 */
final class MyPosts implements ActionInterface
{

    /**
     * MyPosts constructor.
     * @param AuthInterface $auth
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private AuthInterface $auth,
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $user = $this->auth->user($request);
        } catch (NotAuthenticatedException) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Not authenticated',
            ]);
        }

        return new JsonResponse(array_map(
            static fn(Post $post) => [
                'uuid' => (string)$post->uuid(),
                'author' => [
                    'username' => $user->credentials()->username(),
                    'name' => $user->name()->first() . ' ' . $user->name()->last(),
                ],
                'title' => $post->title(),
                'text' => $post->text(),
            ],
            $this->postsRepository->getByAuthor($user->uuid())
        ));
    }
}
