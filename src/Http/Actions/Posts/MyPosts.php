<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\Authentication\NotAuthenticatedException;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MyPosts
 * @package GeekBrains\Blog\Http\Actions\Posts
 */
final class MyPosts implements ActionInterface
{

    /**
     * MyPosts constructor.
     * @param AuthenticationInterface $authentication
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private AuthenticationInterface $authentication,
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws PostsRepositoryException
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $user = $this->authentication->user($request);
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
                    'username' => $user->username(),
                    'name' => $user->name()->first() . ' ' . $user->name()->last(),
                ],
                'title' => $post->title(),
                'text' => $post->text(),
            ],
            $this->postsRepository->getByAuthor($user->uuid())
        ));
    }
}
