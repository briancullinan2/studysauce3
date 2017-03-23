<?php

namespace StudySauce\Bundle\DependencyInjection;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Class DrupalPasswordEncoder
 * @package StudySauce\Bundle\DependencyInjection
 */
class DrupalPasswordEncoder extends BasePasswordEncoder
{
    private $algorithm;
    private $encodeHashAsBase64;
    private $iterationsLog;

    const DRUPAL_MIN_HASH_COUNT = 7;
    const DRUPAL_MAX_HASH_COUNT = 30;
    const DRUPAL_HASH_LENGTH = 43;

    /**
     * Constructor.
     *
     * @param string $algorithm The digest algorithm to use
     * @param bool $encodeHashAsBase64 Whether to base64 encode the password hash
     * @param int $iterationsLog
     * @internal param int $iterations The number of iterations to use to stretch the password hash
     */
    public function __construct($algorithm = 'sha512', $encodeHashAsBase64 = true, $iterationsLog = 15)
    {
        $this->algorithm = $algorithm;
        $this->encodeHashAsBase64 = $encodeHashAsBase64;
        $this->iterationsLog = $iterationsLog;
    }

    /**
     * {@inheritdoc}
     */
    public function encodePassword($raw, $salt)
    {
        // The first 12 characters of an existing hash are its setting string.
        $setting = substr($salt, 0, 12);

        if ($setting[0] != '$' || $setting[2] != '$') {
            return FALSE;
        }
        $count_log2 = self::_password_get_count_log2($setting);
        // Hashes may be imported from elsewhere, so we allow != DRUPAL_HASH_COUNT
        if ($count_log2 < self::DRUPAL_MIN_HASH_COUNT || $count_log2 > self::DRUPAL_MAX_HASH_COUNT) {
            return FALSE;
        }
        $salt = substr($setting, 4, 8);
        // Hashes must have an 8 character salt.
        if (strlen($salt) != 8) {
            return FALSE;
        }

        // Convert the base 2 logarithm into an integer.
        $count = 1 << $count_log2;

        // We rely on the hash() function being available in PHP 5.2+.
        $hash = hash($this->algorithm, $salt . $raw, TRUE);
        do {
            $hash = hash($this->algorithm, $hash . $raw, TRUE);
        } while (--$count);

        $len = strlen($hash);
        $output = self::_password_base64_encode($hash, $len);
        // _password_base64_encode() of a 16 byte MD5 will always be 22 characters.
        // _password_base64_encode() of a 64 byte sha512 will always be 86 characters.
        $expected = ceil((8 * $len) / 6);
        return (strlen($output) == $expected) ? substr($output, 0, self::DRUPAL_HASH_LENGTH) : FALSE;
    }

    /**
     * Encodes bytes into printable base 64 using the *nix standard from crypt().
     *
     * @param $input
     *   The string containing bytes to encode.
     * @param $count
     *   The number of characters (bytes) to encode.
     *
     * @return string Encoded string
     */
    static function _password_base64_encode($input, $count) {
        $output = '';
        $i = 0;
        $itoa64 = self::_password_itoa64();
        do {
            $value = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }

    /**
     * Parse the log2 iteration count from a stored hash or setting string.
     * @param $setting
     * @return int
     */
    static function _password_get_count_log2($setting) {
        $itoa64 = self::_password_itoa64();
        return strpos($itoa64, $setting[3]);
    }

    /**
     * Returns a string for mapping an int to the corresponding base 64 character.
     */
    static function _password_itoa64() {
        return './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return !$this->isPasswordTooLong($raw) && $this->comparePasswords($encoded, $this->encodePassword($raw, $salt));
    }
}