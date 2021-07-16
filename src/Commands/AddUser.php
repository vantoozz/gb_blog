<?php declare(strict_types=1);


namespace GeekBrains\Blog\Commands;


use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Blog\User;
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
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository
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
            ->addArgument('last_name', InputArgument::REQUIRED, 'Last name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->usersRepository->save(
            new User(
                new Name(
                    $input->getArgument('first_name'),
                    $input->getArgument('last_name')
                )
            )
        );

        $output->writeln('User created');

        return Command::SUCCESS;
    }
}
