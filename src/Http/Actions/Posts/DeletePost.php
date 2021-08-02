<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Http\Authentication\NotAuthenticatedException;
use GeekBrains\Blog\Repositories\Posts\PostNotFoundException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @return JsonResponse
     * @throws PostsRepositoryException
     */
    public function handle(Request $request): JsonResponse
    {
        $uuidString = $request->query->get('uuid');

        if (empty($uuidString)) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No UUID',
            ]);
        }

        try {
            $uuid = new UUID($uuidString);
        } catch (InvalidArgumentException) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Malformed UUID',
            ]);
        }

        try {
            $user = $this->authentication->user($request);
        } catch (NotAuthenticatedException) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Not authenticated',
            ]);
        }

        try {
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No such post',
            ]);
        }

        if (!$post->authorUuid()->equals($user->uuid())) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Cannot delete someone else\'s post',
            ]);
        }

        $this->postsRepository->delete($uuid);

        return new JsonResponse(['success' => true]);
    }
}
