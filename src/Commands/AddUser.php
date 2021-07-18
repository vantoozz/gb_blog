<?php declare(strict_types=1);


namespace GeekBrains\Blog\Commands;


use Exception;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\UsersRepository\UserNotFoundException;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Ramsey\Uuid\UuidFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AddUser
 * @package GeekBrains\Blog\Commands
 */
final class AddUser extends Command
{
    /**
     * AddUser constructor.
     * @param UsersRepositoryInterface $usersRepository
     * @param UuidFactoryInterface $uuidFactory
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private UuidFactoryInterface $uuidFactory,
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
                $this->uuidFactory->uuid4(),
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
