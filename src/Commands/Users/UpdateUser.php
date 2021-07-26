<?php declare(strict_types=1);

namespace GeekBrains\Blog\Commands\Users;

use Exception;
use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateUser
 * @package GeekBrains\Blog\Commands\Users
 */
final class UpdateUser extends Command
{
    /**
     * UpdateUser constructor.
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
        parent::__construct('users:update');
    }

    /**
     *
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Updated new user')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addOption(
                'first_name',
                'f',
                InputOption::VALUE_OPTIONAL,
                'First name',
            )
            ->addOption(
                'last_name',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Last name',
            )
            ->addOption(
                'password',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Password',
            );
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstName = $input->getOption('first_name');
        $lastName = $input->getOption('last_name');
        $password = $input->getOption('password');

        if (empty($firstName) && empty($lastName) && empty($password)) {
            $output->writeln('Nothing to update');
            return Command::SUCCESS;
        }

        $username = $input->getArgument('username');

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            $output->writeln("User not found: $username");
            return Command::FAILURE;
        }

        $this->usersRepository->save(
            new User(
                $user->uuid(),
                new Name(
                    $firstName ?? $user->name()->first(),
                    $lastName ?? $user->name()->last(),
                ),
                empty($password) ?
                    $user->credentials() :
                    Credentials::createFrom(
                        $user->credentials()->username(),
                        $password
                    )
            )
        );

        $output->writeln("User updated: $username");

        return Command::SUCCESS;
    }

}
