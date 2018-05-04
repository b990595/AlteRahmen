<?php

use Zend\Crypt\Hash as CryptHash;
use Zend\Crypt\Hmac as CryptHmac;

class SharedSecret
{

    /**
     * @var string
     */
    private $secret;

    /**
     * Supported algorithms
     *
     * @var array
     */
    private $methods = array('mac', 'hmac');

    /**
     * Supported algorithms
     *
     * @var array
     */
    private $algorithms = array('sha1', 'sha256', 'sha384', 'sha512');

    public function __construct()
    {
        $this->secret = FR_SHAREDSECRET;


    }

    /**
     * Generate digest
     *
     * @param string $method
     * @param string $algorithm
     * @param $timestamp
     * @param string $username
     * @return string
     */
    public function computeDigest($method, $algorithm, $timestamp, $username)
    {
        if (!in_array($method, $this->getMethods())) {
            throw new InvalidArgumentException("Invalid method!");
        }

        if (!in_array($algorithm, $this->getAlgorithms())) {
            throw new InvalidArgumentException("Invalid algorithm!");
        }

        // Real HMAC: @see https://en.wikipedia.org/wiki/Hash-based_message_authentication_code
        if ($method === 'hmac') {
            return CryptHmac::compute($this->secret . $timestamp, $algorithm, $username);
        }
        // Simple HMAC: Ex sha1(secret . sha1(secret . username))
        elseif ($method === 'mac') {
            return CryptHash::compute($algorithm, $this->secret . $timestamp . CryptHash::compute($algorithm, $this->secret . $timestamp . $username));
        }

        return null;
    }

    /**
     * Generate authentication password
     *
     * @param string $method
     * @param string $algorithm
     * @param string $timestamp
     * @param string $username
     * @return string
     * @throws Exception
     */
    public function generateAuthenticationPassword($method, $algorithm, $timestamp, $username)
    {
        if (($digest = $this->computeDigest($method, $algorithm, $timestamp, $username))) {
            return '{' . "$method-$algorithm-$timestamp" . '}' . $digest;
        }
        throw new \Exception("SharedSecret: generateAuthenticationPassword()");
    }

    /**
     * Generate valid authentication header
     *
     * @param string $method
     * @param string $algorithm
     * @param string $timestamp
     * @param string $username
     * @return string
     */
    public function generateAuthenticationHeader($method, $algorithm, $timestamp, $username)
    {
        return $username . ':' . $this->generateAuthenticationPassword($method, $algorithm, $timestamp, $username);
    }

    /**
     * Generate password
     *
     * @param string $username
     * @return string
     */
    public function getPassword($username)
    {
        return $this->generateAuthenticationPassword('hmac', 'sha256', time(), $username);
    }

    /**
     * Get supported methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get supported algorithms
     *
     * @return array
     */
    public function getAlgorithms()
    {
        return $this->algorithms;
    }

}