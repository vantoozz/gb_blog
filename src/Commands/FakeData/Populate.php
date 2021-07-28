<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\FakeData;

use Exception;
use Faker\Generator;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\CommentId;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Exceptions\RuntimeException;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryException;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Populate
 * @package GeekBrains\Blog\Commands\FakeData
 */
final class Populate extends Command
{
    /**
     * Populate constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param PostsRepositoryInterface $postsRepository
     * @param CommentsRepositoryInterface $commentsRepository
     * @param Generator $faker
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
        private Generator $faker,
    ) {
        parent::__construct('fake-data:populate');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Populates repositories with fake data');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws UsersRepositoryException
     * @throws PostsRepositoryException
     * @throws CommentsRepositoryException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = array_map(fn() => $this->makeUser(), range(1, 50));

        $posts = array_map(fn() => $this->makePost($users[array_rand($users)]->uuid()), range(1, 100));

        foreach ($posts as $post) {
            $this->makeComments($post->uuid(), $users, 0);
        }

        return Command::SUCCESS;
    }

    /**
     * @throws UsersRepositoryException
     */
    private function makeUser(): User
    {
        $user = new User(
            UUID::random(),
            new Name($this->faker->firstName, $this->faker->lastName),
            Credentials::createFrom(
                $this->faker->userName,
                $this->faker->password
            )
        );

        $this->usersRepository->save($user);

        return $user;
    }

    /**
     * @param UUID $authorUuid
     * @return Post
     * @throws PostsRepositoryException
     */
    private function makePost(UUID $authorUuid): Post
    {
        $post = new Post(
            UUID::random(),
            $authorUuid,
            $this->faker->sentence(6, true),
            $this->faker->realText
        );

        $this->postsRepository->save($post);

        return $post;
    }

    /**
     * @param UUID $parentUuid
     * @param array $users
     * @param int $level
     * @throws CommentsRepositoryException
     */
    private function makeComments(UUID $parentUuid, array $users, int $level): void
    {
        for ($i = 0; $i < $this->randomNumber(7 - $level); $i++) {
            $commentUuid = UUID::random();

            $comment = new Comment(
                new CommentId($parentUuid, $commentUuid),
                $users[array_rand($users)]->uuid(),
                $this->faker->sentence
            );

            $this->commentsRepository->save($comment);

            $this->makeComments($commentUuid, $users, ++$level);
        }
    }

    /**
     * @param int $max
     * @return int
     */
    private function randomNumber(int $max): int
    {
        try {
            return random_int(0, $max);
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
