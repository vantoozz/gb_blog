<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Http\Authentication\AuthenticationInterface;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DeletePost implements ActionInterface
{

    /**
     * DeletePost constructor.
     * @param PostsRepositoryInterface $postsRepository
     * @param AuthenticationInterface $auth
     */
    public function __construct(
        private AuthenticationInterface $auth,
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
            $uuid = new UUID($request->query->get('uuid'));
        } catch (InvalidArgumentException) {
            return new JsonResponse(['success' => false]);
        }

        return new JsonResponse(['success' => true]);
    }
}
