<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Users;

use Exception;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateUser
 * @package GeekBrains\Blog\Commands\Users
 */
final class CreateUser extends Command
{
    /**
     * CreateUser constructor.
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
    ) {
        parent::__construct('users:create');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Creates new user')
            ->addArgument('first_name', InputArgument::REQUIRED, 'First name')
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'Password');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');

        if ($this->usernameTaken($username)) {
            $output->writeln("Username already taken: $username");
            return Command::FAILURE;
        }

        $this->usersRepository->save(
            new User(
                UUID::random(),
                new Name(
                    $input->getArgument('first_name'),
                    $input->getArgument('last_name')
                ),
                Credentials::createFrom(
                    $username,
                    $input->getArgument('password')
                )
            )
        );

        $output->writeln("User created: $username");

        return Command::SUCCESS;
    }

    /**
     * @param string $username
     * @return bool
     */
    private function usernameTaken(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
