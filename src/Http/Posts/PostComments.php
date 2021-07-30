<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Posts;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\ActionInterface;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryException;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\PostNotFoundException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostComments
 * @package GeekBrains\Blog\Http\Posts
 */
final class PostComments implements ActionInterface
{

    /**
     * PostComments constructor.
     * @param CommentsRepositoryInterface $commentsRepository
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws PostsRepositoryException
     * @throws CommentsRepositoryException
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
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No such post',
            ]);
        }

        $heap = array_map(
            static fn(Comment $comment) => [
                'uuid' => (string)$comment->uuid(),
                'parent' => (string)$comment->parentUuid(),
                'author' => (string)$comment->authorUuid(),
                'text' => $comment->text(),
            ],
            $this->commentsRepository->getChildren($post->uuid())
        );


        return new JsonResponse($this->tree($heap, (string)$post->uuid()));
    }

    /**
     * @param array $flattenComments
     * @param string $baseUuid
     * @return array
     */
    private function tree(array $flattenComments, string $baseUuid): array
    {
        $tree = [];

        $remainingComments = [];

        foreach ($flattenComments as $comment) {
            if ($baseUuid === $comment['parent']) {
                $tree[] = [
                    'uuid' => $comment['uuid'],
                    'author' => $comment['author'],
                    'text' => $comment['text'],
                ];
                continue;
            }
            $remainingComments[] = $comment;
        }

        foreach ($tree as &$comment) {
            $children = $this->tree($remainingComments, $comment['uuid']);
            if (!empty($children)) {
                $comment['comments'] = $children;
            }
        }

        return $tree;
    }
}
