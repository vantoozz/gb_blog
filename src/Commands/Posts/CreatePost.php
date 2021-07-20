<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Posts;

use Exception;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreatePost
 * @package GeekBrains\Blog\Commands\Users
 */
final class CreatePost extends Command
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private UuidFactoryInterface $uuidFactory,
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


        $uuid = new UUID($this->uuidFactory->uuid4()->toString());

        $this->postsRepository->save(
            new Post(
                $uuid,
                $user->uuid(),
                $input->getArgument('title'),
                $input->getArgument('text'),
            )
        );

        $output->writeln("Posts created: $uuid");

        return Command::SUCCESS;
    }
}
