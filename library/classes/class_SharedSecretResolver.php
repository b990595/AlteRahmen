<?php

use Zend\Crypt\Utils as CryptUtils;


class SharedSecretResolver
{

    /**
     * @var SharedSecret
     */
    protected $sharedSecret;

    /**
     * @var string
     */
    private $disableTimeValidation = false;

    public function __construct(SharedSecret $sharedSecret)
    {
        $this->sharedSecret = $sharedSecret;
    }

    /**
     * Resolve credentials
     *
     * @param  string $username Username
     * @param  string $password The password to authenticate
     * @return boolean
     * @throws InvalidArgumentException
     */
    public function resolve(string $username, string $password): bool
    {

        if (empty($username)) {
            throw new InvalidArgumentException('Username is required');
        }

        if (empty($password)) {
            throw new InvalidArgumentException('Password is required');
        }

        try {
            list($method, $algorithm, $timestamp, $digest) = array_values($this->parseAuthenticationPassword($password));
        } catch (\Exception $e) {
            return false;
        }

        // Generated password is only shortly available. 5 minutes allows for a little time skew.
        $timestampValid = (int)$timestamp > strtotime('-5 minutes') && $timestamp < strtotime('+5 minutes') || $this->disableTimeValidation;

        // Real HMAC: @see https://en.wikipedia.org/wiki/Hash-based_message_authentication_code
        if ($timestampValid && CryptUtils::compareStrings($this->sharedSecret->computeDigest($method, $algorithm, $timestamp, $username), $digest)) {
            return true;
        }


        return false;
    }

    /**
     * Parse the password string. Format should be:
     * {method-algorithm-timestamp}digest
     *
     * @param string $password
     * @return array
     * @throws Exception
     */
    protected function parseAuthenticationPassword($password)
    {
        $methods = implode('|', array_map(function ($value) {
            return preg_quote($value);
        }, $this->sharedSecret->getMethods()));

        $algorithms = implode('|', array_map(function ($value) {
            return preg_quote($value);
        }, $this->sharedSecret->getAlgorithms()));

        $matches = array();
        $match = preg_match("/^\\{(?P<method>$methods)\\-(?P<algorithm>$algorithms)\\-(?P<timestamp>[0-9]+)\\}(?P<digest>[a-z0-9]+)/", $password, $matches);

        if ($match) {
            return array(
                'method' => $matches['method'],
                'algorithm' => $matches['algorithm'],
                'timestamp' => $matches['timestamp'],
                'digest' => $matches['digest'],
            );
        } else {
            throw new \Exception("Bad digest!");
        }
    }

}