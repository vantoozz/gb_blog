<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\Authentication\NotAuthenticatedException;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\Posts\PostNotFoundException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;

/**
 * Class DeletePost
 * @package GeekBrains\Blog\Http\Actions\Posts
 */
final class DeletePost implements ActionInterface
{

    /**
     * DeletePost constructor.
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
     * @return Response
     * @throws PostsRepositoryException
     */
    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException) {
            return new ErrorResponse('Malformed UUID');
        }

        try {
            $user = $this->authentication->user($request);
        } catch (NotAuthenticatedException) {
            return new ErrorResponse('Not authenticated');
        }

        try {
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return new ErrorResponse('No such post');
        }

        if (!$post->authorUuid()->equals($user->uuid())) {
            return new ErrorResponse('Cannot delete someone else\'s post');
        }

        $this->postsRepository->delete($uuid);

        return new SuccessfulResponse(['deleted' => (string)$uuid]);
    }
}
