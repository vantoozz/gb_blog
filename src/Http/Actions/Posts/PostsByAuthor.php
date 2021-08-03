<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;

/**
 * Class PostsByAuthor
 * @package GeekBrains\Blog\Actions\Http
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
     * @return Response
     * @throws UsersRepositoryException
     * @throws PostsRepositoryException
     */
    public function handle(Request $request): Response
    {
        try {
            $username = $request->query('username');
        } catch (HttpException) {
            return new SuccessfulResponse([]);
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return new SuccessfulResponse([]);
        }

        return new SuccessfulResponse(array_map(
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
