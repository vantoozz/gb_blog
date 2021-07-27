<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Posts;

use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostsByAuthor
 * @package GeekBrains\Blog\Http
 */
final class PostsByAuthor implements ActionInterface
{

    /**
     * PostsByAuthor constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            return new JsonResponse([]);
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return new JsonResponse([]);
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
