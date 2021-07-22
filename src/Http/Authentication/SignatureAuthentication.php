<?php declare(strict_types=1);

namespace GeekBrains\Blog\Http\Authentication;

use GeekBrains\Blog\Repositories\Users\UserNotFoundException;
use GeekBrains\Blog\Repositories\Users\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\HttpFoundation\Request;

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
     */
    public function user(Request $request): User
    {
        $header = $request->headers->get('Authorization');

        if (empty($header)) {
            throw new NotAuthenticatedException('Not authorization header');
        }

        if (0 !== stripos(mb_strtolower($header), self::SIGNATURE_PREFIX)) {
            throw new NotAuthenticatedException('Malformed authorization header');
        }

        $token = substr($header, strlen(self::SIGNATURE_PREFIX));

        $parts = explode(':', $token);

        if (count($parts) !== 2) {
            throw new NotAuthenticatedException('Malformed token');
        }

        $username = $parts[0];
        if (empty($username)) {
            throw new NotAuthenticatedException('No username in token');
        }

        $username = $parts[0];
        if (empty($username)) {
            throw new NotAuthenticatedException('No username in token');
        }

        $signature = $parts[1];
        if (empty($signature)) {
            throw new NotAuthenticatedException('No signature in token');
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
}
