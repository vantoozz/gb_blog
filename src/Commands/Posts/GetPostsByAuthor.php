<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Posts;

use Exception;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GetPostsByAuthor
 * @package GeekBrains\Blog\Commands\Posts
 */
final class GetPostsByAuthor extends Command
{

    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
    ) {
        parent::__construct('posts:by-author');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Finds posts by author')
            ->addArgument('username', InputArgument::REQUIRED, 'Username');
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

        $table = new Table($output);

        $table
            ->setHeaders(['UUID', 'Title', 'Text'])
            ->setRows(array_map(
                static fn(Post $post) => [
                    $post->uuid(),
                    $post->title(),
                    mb_strimwidth($post->text(), 0, 50, '...'),
                ],
                $this->postsRepository->getByAuthor($user->uuid())
            ));

        $table->render();

        return Command::SUCCESS;
    }
}
