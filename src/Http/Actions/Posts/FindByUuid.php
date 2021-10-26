<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Actions\Posts;


use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Repositories\PostsRepository\PostNotFoundException;
use GeekBrains\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;

class FindByUuid implements ActionInterface
{

    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $uuid = new UUID($request->query('uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get($uuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new SuccessfulResponse([
            'uuid' => $post->uuid(),
            'title' => $post->title(),
            'text' => $post->text(),
        ]);
    }
}
