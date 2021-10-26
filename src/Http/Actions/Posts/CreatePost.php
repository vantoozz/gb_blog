<?php

namespace GeekBrains\Blog\Http\Actions\Posts;

use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Http\Actions\ActionInterface;
use GeekBrains\Blog\Http\ErrorResponse;
use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Http\Response;
use GeekBrains\Blog\Http\SuccessfulResponse;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Blog\UUID;

class CreatePost implements ActionInterface
{

    // Внедряем репозиторий статей
    public function __construct(
        private PostsRepositoryInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Генерируем UUID для новой статьи
        $newPostUuid = UUID::random();

        try {
            // Пытаемся создать объект статьи
            // из данных запроса
            $post = new Post(
                $newPostUuid,
                new UUID($request->jsonBodyField('author_uuid')),
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        // Сохраняем новую статью в репозитории
        $this->postsRepository->save($post);

        // Возвращаем успешный ответ,
        // содержащий UUID новой статьи
        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
