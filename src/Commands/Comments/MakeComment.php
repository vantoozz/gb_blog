<?php declare(strict_types=1);


namespace GeekBrains\Blog\Commands\Comments;


use Exception;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class MakeComment
 * @package GeekBrains\Blog\Commands\Comments
 */
final class MakeComment extends Command
{
    /**
     * MakeComment constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param PostsRepositoryInterface $postsRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct('comments:make');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Makes a comment')
            ->addArgument('uuid', InputArgument::REQUIRED, 'Post UUID')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
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

        $postUUID = new UUID($input->getArgument('uuid'));

        try {
            $post = $this->postsRepository->get($postUUID);
        } catch (UserNotFoundException) {
            $output->writeln("Post not found: $postUUID");
            return Command::FAILURE;
        }

        $uuid = UUID::random();

        new Comment(
            $uuid,
            $post->uuid(),
            $user->uuid(),
            $input->getArgument('text')
        );

        return Command::SUCCESS;
    }
}
