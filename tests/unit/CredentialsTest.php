<?php declare(strict_types=1);

namespace GeekBrains\Blog\UnitTests;

use GeekBrains\Blog\Credentials;
use PHPUnit\Framework\TestCase;

/**
 *
 */
final class CredentialsTest extends TestCase
{

    /**
     * @test
     */
    public function it_validates_password(): void
    {
        $credentials = new Credentials(
            'some_user',
            '501e6611bd6dbb492dae17bfc9ef3604' .
            'e17da181451813bbaebd23fb7f4d1449' .
            '59495e094325649446f66cdb017ca91c' .
            '45c35620bad31a71606131cdee4a828a',
            '89aa44fceb0e42a9b28b3e310cc77fa70226384d' .
            '96e05c98aad3e8029958a98cb78207d4e8840eda'
        );

        $this->assertTrue($credentials->check('some_password'));
        $this->assertFalse($credentials->check('wrong_password'));
    }

    /**
     * @test
     */
    public function it_hashes_password(): void
    {
        $credentials = Credentials::createFrom('some_user', 'some_password');

        $this->assertEquals('some_user', $credentials->username());
        $this->assertNotEquals('some_password', $credentials->hashedPassword());
        $this->assertTrue($credentials->check('some_password'));
    }


    /**
     * @test
     */
    public function it_stores_username(): void
    {
        $credentials = new Credentials('username', 'password', 'salt');

        $storedUsername = $credentials->username();

        $this->assertEquals('username', $storedUsername);
    }

    /**
     * @test
     */
    public function it_stores_hashed_password(): void
    {
        $credentials = new Credentials('username', 'hashed_password', 'salt');

        $storedHashedPassword = $credentials->hashedPassword();

        $this->assertEquals('hashed_password', $storedHashedPassword);
    }

    /**
     * @test
     */
    public function it_stores_salt(): void
    {
        $credentials = new Credentials('username', 'hashed_password', 'salt');

        $storedSalt = $credentials->salt();

        $this->assertEquals('salt', $storedSalt);
    }
}
