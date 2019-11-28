<?php
namespace Tricolore\Security\PasswordEncoder;

class BCryptEncoder
{
    /**
     * Encode password
     * 
     * @param string $raw_password
     * @param array $options 
     * @return string
     */
    public static function passwordHash($raw_password, array $options = [])
    {
        return password_hash($raw_password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Determine if the password hash needs to be rehashed according to the options provided
     *
     * @param string $password_hash
     * @param array $options
     * @return bool
     */
    public static function passwordNeedsRehash($password_hash, array $options = [])
    {
        return password_needs_rehash($password_hash, PASSWORD_BCRYPT, $options);
    }

    /**
     * Verify password
     * 
     * @param string $raw_password
     * @param string $password_hash
     * @return bool
     */
    public static function passwordVerify($raw_password, $password_hash)
    {
        return password_verify($raw_password, $password_hash);
    }

    /**
     * Password info
     * 
     * @param string $password_hash 
     * @return array
     */
    public static function passwordInfo($password_hash)
    {
        return password_get_info($password_hash);
    }
}
