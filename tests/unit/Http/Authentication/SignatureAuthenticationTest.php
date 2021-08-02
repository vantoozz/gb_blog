<?php declare(strict_types=1);

namespace GeekBrains\Blog\UnitTests\Http\Authentication;

use GeekBrains\Blog\Credentials;
use GeekBrains\Blog\Http\Authentication\NotAuthenticatedException;
use GeekBrains\Blog\Http\Authentication\SignatureAuthentication;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use GeekBrains\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */
final class SignatureAuthenticationTest extends TestCase
{

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_no_header_in_request(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('No authorization header');

        $authentication->user(new Request());
    }

    /**
     * @param User[] $users
     * @return UsersRepositoryInterface
     */
    private function dummyUsersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface {

            /**
             * @param User[] $users
             */
            public function __construct(
                private array $users
            ) {
            }

            /**
             * @param string $username
             * @return User
             */
            public function getByUsername(string $username): User
            {
                foreach ($this->users as $user) {
                    if ($user->username() === $username) {
                        return $user;
                    }
                }

                throw new UserNotFoundException("No such user: $username");
            }

            /**
             * @param User $user
             */
            public function save(User $user): void
            {
            }
        };
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_no_prefix_in_header(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Malformed authorization header');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'some_user']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_token_malformed(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Malformed token');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'bearer some_user']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_no_signature_in_token(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('No signature in token');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'bearer some_user:']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_no_username_in_token(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('No username in token');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'bearer :some_signature']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_there_is_no_such_user(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('No such user: some_user');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'bearer some_user:some_signature']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     */
    public function it_throws_an_exception_if_wrong_signature(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([
            new User(
                UUID::random(),
                new Name('Some', 'Name'),
                Credentials::createFrom('some_user', 'some_password')
            ),
        ]));

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Wrong signature');

        $authentication->user(new Request(server: ['HTTP_AUTHORIZATION' => 'bearer some_user:some_signature']));
    }

    /**
     * @test
     * @throws UsersRepositoryException
     * @throws NotAuthenticatedException
     */
    public function it_returns_user(): void
    {
        $userUuid = new UUID('294a9a4a-f209-4503-b3bc-f8aaf394bf51');

        $authentication = new SignatureAuthentication($this->dummyUsersRepository([
            new User(
                $userUuid,
                new Name('Some', 'Name'),
                Credentials::createFrom('some_user', 'some_password')
            ),
        ]));

        $authenticatedUser = $authentication->user(new Request(server: [
            'HTTP_AUTHORIZATION' => 'bearer some_user:1068111ba18b9010d44a299f81793510f91c33745739842bfa17be94094cf350',
        ]));

        $this->assertEquals($userUuid, $authenticatedUser->uuid());
    }

    /**
     * @test
     */
    public function it_generates_the_same_token_every_time(): void
    {
        $authentication = new SignatureAuthentication($this->dummyUsersRepository([]));

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
