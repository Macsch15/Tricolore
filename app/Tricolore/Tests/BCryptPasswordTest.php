<?php
namespace Tricolore\Tests;

use Tricolore\Security\PasswordEncoder\BCryptEncoder;

class BCryptPasswordTest extends \PHPUnit_Framework_TestCase
{
    public function testBcryptHasher()
    {
        $my_password = 'Example super password!';
        $salt = 'Lorem ipsum Anim ullamco magna Duis non ut exercitation in reprehenderit';
        $cost = 10;

        $expected = '$2y$10$TG9yZW0gaXBzdW0gQW5pbOs6IDnjToYF4xRiNTmg5hBu8BljuL2Pu';
        $actual = BCryptEncoder::passwordHash($my_password, [
            'salt' => $salt,
            'cost' => $cost
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testBcryptHasherLen()
    {
        $my_password = 'Example super password!';
        $hash = BCryptEncoder::passwordHash($my_password);

        $expected = 60;
        $actual = strlen($hash);

        $this->assertEquals($expected, $actual);
    }

    public function testPasswordVerify()
    {
        $my_password = 'Example super password!';
        $hash = BCryptEncoder::passwordHash($my_password);

        $this->assertTrue(BCryptEncoder::passwordVerify($my_password, $hash));
    }

    public function testAlgo()
    {
        $my_password = 'Example super password!';
        $hash = BCryptEncoder::passwordHash($my_password);

        $expected = 'bcrypt';
        $actual = BCryptEncoder::passwordInfo($hash)['algoName'];

        $this->assertEquals($expected, $actual);
    }

    public function testNeedsRehash()
    {
        $my_password = 'Example super password!';
        $old_password_hash = BCryptEncoder::passwordHash($my_password, [
            'salt' => 'Duis non ut exercitation in reprehenderit',
            'cost' => 11
        ]);

        $new_options = [
            'salt' => 'Lorem ipsum Anim ullamco magna Duis non ut exercitation in reprehenderit',
            'cost' => 10
        ];

        $actual = BCryptEncoder::passwordNeedsRehash($old_password_hash, $new_options);

        $this->assertTrue($actual);
    }
}
