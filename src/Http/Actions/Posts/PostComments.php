<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryException;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\PostNotFoundException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;

/**
 * Class PostComments
 * @package GeekBrains\Blog\Http\Actions\Posts
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
     * @return Response
     * @throws PostsRepositoryException
     * @throws CommentsRepositoryException
     */
    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException) {
            return new ErrorResponse('Malformed UUID');
        }

        try {
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return new ErrorResponse('No such post');
        }

        $comments = array_map(
            static fn(Comment $comment) => [
                'uuid' => (string)$comment->uuid(),
                'parent' => (string)$comment->parentUuid(),
                'author' => (string)$comment->authorUuid(),
                'text' => $comment->text(),
            ],
            $this->commentsRepository->getChildren($post->uuid())
        );

        return new SuccessfulResponse($this->tree($comments, (string)$post->uuid()));
    }

    /**
     * @param array $heap
     * @param string $parent
     * @return array
     */
    private function tree(array $heap, string $parent): array
    {
        $branch = [];
        $leftovers = [];

        foreach ($heap as $comment) {
            if ($parent === $comment['parent']) {
                $branch[] = [
                    'uuid' => $comment['uuid'],
                    'author' => $comment['author'],
                    'text' => $comment['text'],
                ];
                continue;
            }
            $leftovers[] = $comment;
        }

        foreach ($branch as &$comment) {
            $leaves = $this->tree($leftovers, $comment['uuid']);
            if (!empty($leaves)) {
                $comment['comments'] = $leaves;
            }
        }

        return $branch;
    }
}
