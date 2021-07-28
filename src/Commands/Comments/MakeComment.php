<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Comments;

use Exception;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\CommentId;
use GeekBrains\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\Blog\Repositories\Comments\CommentNotFoundException;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryException;
use GeekBrains\Blog\Repositories\Comments\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\Posts\PostNotFoundException;
use GeekBrains\Blog\Repositories\Posts\PostsRepositoryException;
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
     * @param CommentsRepositoryInterface $commentsRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
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
            ->addArgument('uuid', InputArgument::REQUIRED, 'Commentable UUID')
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

        $this->commentsRepository->save(
            new Comment(
                $this->makeCommentId(new UUID($input->getArgument('uuid'))),
                $user->uuid(),
                $input->getArgument('text')
            )
        );

        return Command::SUCCESS;
    }

    /**
     * @param UUID $parentUuid
     * @return CommentId
     * @throws PostsRepositoryException
     * @throws CommentsRepositoryException
     * @throws InvalidArgumentException
     */
    private function makeCommentId(UUID $parentUuid): CommentId
    {
        if ($this->isPostUuid($parentUuid)) {
            return CommentId::forPost($parentUuid, UUID::random());
        }

        if ($this->isCommentUuid($parentUuid)) {
            return CommentId::forComment($parentUuid, UUID::random());
        }

        throw new InvalidArgumentException("Cannot find commentable: $parentUuid");
    }

    /**
     * @param UUID $uuid
     * @return bool
     * @throws PostsRepositoryException
     */
    private function isPostUuid(UUID $uuid): bool
    {
        try {
            $this->postsRepository->get($uuid);
        } catch (PostNotFoundException) {
            return false;
        }
        return true;
    }

    /**
     * @param UUID $uuid
     * @return bool
     * @throws CommentsRepositoryException
     */
    private function isCommentUuid(UUID $uuid): bool
    {
        try {
            $this->commentsRepository->get($uuid);
        } catch (CommentNotFoundException) {
            return false;
        }
        return true;
    }
}
