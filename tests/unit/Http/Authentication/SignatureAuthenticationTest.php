<?php declare(strict_types=1);


namespace GeekBrains\Blog\UnitTests\Http\Authentication;


use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Http\Authentication\SignatureAuthentication;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PHPUnit\Framework\TestCase;

/**
 *
 */
final class SignatureAuthenticationTest extends TestCase
{

    /**
     * @test
     */
    public function it_generates_the_same_token_every_time(): void
    {
        $usersRepository = new class implements UsersRepositoryInterface {

            /**
             * @param string $username
             * @return User
             */
            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("");
            }

            /**
             * @param User $user
             */
            public function save(User $user): void
            {
            }
        };


        $authentication = new SignatureAuthentication($usersRepository);

        $user = new User(
            UUID::random(),
            new Name('Some', 'User'),
            Credentials::createFrom('some_user', 'some_password')
        );

        $firstToken = $authentication->token($user);
        $secondToken = $authentication->token($user);

        $this->assertEquals($firstToken, $secondToken);
    }
}
