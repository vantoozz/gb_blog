<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Posts;

use Exception;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePost
 * @package GeekBrains\Blog\Commands\Posts
 */
final class CreatePost extends Command
{
    /**
     * CreatePost constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct('posts:create');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates new post')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('title', InputArgument::REQUIRED, 'Title')
            ->addArgument('text', InputArgument::REQUIRED, 'Text');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            $output->writeln("User not found: $username");
            return Command::FAILURE;
        }

        $uuid = UUID::random();

        $this->postsRepository->save(
            new Post(
                $uuid,
                $user->uuid(),
                $input->getArgument('title'),
                $input->getArgument('text'),
            )
        );

        $output->writeln("Post created: $uuid");

        return Command::SUCCESS;
    }
}
