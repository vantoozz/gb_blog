<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Authentication;

use GeekBrains\Blog\Http\HttpException;
use GeekBrains\Blog\Http\Request;
use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;

/**
 * Class SignatureAuthentication
 * @package GeekBrains\Blog\Http\Authentication
 */
final class SignatureAuthentication implements AuthenticationInterface
{
    /**
     *
     */
    private const SECRET = 'some secret string';
    /**
     *
     */
    private const SIGNATURE_PREFIX = 'bearer ';
    /**
     *
     */
    private const TOKEN_DELIMITER = ':';

    /**
     * SignatureAuthentication constructor.
     * @param UsersRepositoryInterface $usersRepository
     */
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }

    /**
     * @param Request $request
     * @return User
     * @throws NotAuthenticatedException
     * @throws UsersRepositoryException
     */
    public function user(Request $request): User
    {
        try {
            $header = $request->header('Authorization');
        } catch (HttpException) {
            throw new NotAuthenticatedException('No authorization header');
        }

        if (0 !== mb_stripos(mb_strtolower($header), self::SIGNATURE_PREFIX)) {
            throw new NotAuthenticatedException('Malformed authorization header');
        }

        $token = mb_substr($header, strlen(self::SIGNATURE_PREFIX));

        $parts = explode(self::TOKEN_DELIMITER, $token);

        if (count($parts) < 2) {
            throw new NotAuthenticatedException('Malformed token');
        }

        $signature = array_pop($parts);
        if (empty($signature)) {
            throw new NotAuthenticatedException('No signature in token');
        }

        $username = implode(self::TOKEN_DELIMITER, $parts);
        if (empty($username)) {
            throw new NotAuthenticatedException('No username in token');
        }

        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            throw new NotAuthenticatedException("No such user: $username");
        }

        if ($this->signature($user) !== $signature) {
            throw new NotAuthenticatedException('Wrong signature');
        }

        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    private function signature(User $user): string
    {
        return hash('sha256', $user->uuid() . self::SECRET);
    }

    /**
     * @param User $user
     * @return string
     */
    public function token(User $user): string
    {
        return $user->username() . self::TOKEN_DELIMITER . $this->signature($user);
    }
}
