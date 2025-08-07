<?php

namespace Nextvikas\Authenticator\Helpers;

use Illuminate\Support\Facades\Config;
use RuntimeException;
use Illuminate\Http\Request;
use InvalidArgumentException;


class Authenticator
{
    protected int $length; // Number of digits in the OTP
    protected int $period; // Time step in seconds
    protected string $algorithm; // Hashing algorithm (e.g., 'sha1', 'sha256', 'sha512')
    protected int $window; // Number of time steps to check backward/forward for verification

    /**
     * @var string Base32 character set for encoding/decoding.
     */
    protected array $base32Chars = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z', '2', '3', '4', '5', '6', '7',
        '=', // Padding character
    ];

    /**
     * @var array Flipped Base32 character set for efficient lookup.
     */
    protected array $base32CharsFlipped;

    public function __construct()
    {
        // Initialize properties from package configuration
        $settings = Config::get('authenticator.otp_settings', []);
        $this->algorithm = strtolower($settings['otp_algorithm'] ?? 'sha1');

        $supported = ['sha1', 'sha256', 'sha512'];
        if (!in_array($this->algorithm, $supported)) {
            throw new InvalidArgumentException("Unsupported algorithm: {$this->algorithm}");
        }

        $this->length = $settings['otp_digits'] ?? 6;
        $this->period = $settings['otp_period'] ?? 30;
        $this->window = strtolower($settings['otp_window'] ?? 1);
        $this->base32CharsFlipped = array_flip($this->base32Chars);
    }

    /**
     * Generates a random Base32 encoded secret key.
     *
     * @param int $secretLength Length of the random bytes to generate (e.g., 16 for 80 bits Base32).
     * @return string Base32 encoded secret.
     * @throws RuntimeException If a secure random source is unavailable.
     */
    public function generateRandomSecret(int $secretLength = 16): string
    {
        // Secret length for random_bytes is bytes, 16 bytes = 128 bits = ~26 chars Base32
        if ($secretLength < 16 || $secretLength > 128) { // Minimum 16 bytes for 80-bit equivalent
            throw new RuntimeException('Invalid secret length for random byte generation. Must be between 16 and 128 bytes.');
        }

        $randomBytes = false;
        if (function_exists('random_bytes')) {
            try {
                $randomBytes = random_bytes($secretLength);
            } catch (\Exception $e) {
                // Handle possible exceptions from random_bytes
            }
        }

        if ($randomBytes === false) {
            throw new RuntimeException('Cannot create secure random secret due to source unavailability. Consider enabling PHP\'s OpenSSL or ensuring sufficient entropy.');
        }

        // Convert random bytes to Base32
        return $this->base32Encode($randomBytes);
    }

    /**
     * Generates a One-Time Password (OTP) for a given secret and time slice.
     * Implements RFC 6238.
     *
     * @param string $secret The Base32 encoded secret key.
     * @param int|null $timeSlice The time slice to use (defaults to current time slice).
     * @return string The generated OTP code.
     */
    public function getCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = floor(time() / $this->period); // Use configured period
        }

        $secretKeyBytes = $this->base32Decode($secret); // Use internal Base32 decoder

        // Pack time slice into a 64-bit big-endian integer
        $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

        // Calculate HMAC hash
        $hm = hash_hmac($this->algorithm, $time, $secretKeyBytes, true); // Use configured algorithm

        // Extract 4 bytes from the hash
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);

        // Convert to integer and apply dynamic modulo
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF; // Clear the most significant bit

        $modulo = pow(10, $this->length); // Use configured length

        return str_pad($value % $modulo, $this->length, '0', STR_PAD_LEFT);
    }

    /**
     * Generates a URL for a QR code image.
     *
     * @param string $name Display name for the OTP entry in the app.
     * @param string $secret The Base32 encoded secret key.
     * @param string|null $issuer The issuer/company name (displayed above the OTP code).
     * @return string The URL to the QR code image.
     */
    public function getQR(string $name, string $secret, ?string $issuer = null): string
    {
        // **IMPORTANT:** quickchart.io is a third-party service.
        // For a top-level, dependency-free package, you should consider
        // implementing QR code generation locally using a PHP library like
        // bacon/bacon-qr-code (which would add a dependency) or writing
        // a much more complex pure PHP QR generator (very hard).
        // For now, retaining quickchart.io but with strong recommendation to change.

        $urlencoded = urlencode('otpauth://totp/' . $name . '?secret=' . $secret);

        // Add algorithm, digits, and period to the QR code URL for better compatibility
        $urlencoded .= urlencode('&algorithm=' . strtoupper($this->algorithm));
        $urlencoded .= urlencode('&digits=' . $this->length);
        $urlencoded .= urlencode('&period=' . $this->period);

        if (isset($issuer)) {
            $urlencoded .= urlencode('&issuer=' . urlencode($issuer));
        }

        // Default QR code parameters (can be made configurable if needed)
        $width = 200;
        $height = 200;
        $level = 'M'; // Error correction level (L, M, Q, H)

        return 'https://quickchart.io/chart?chs=' . $width . 'x' . $height . '&chld=' . $level . '|0&cht=qr&chl=' . $urlencoded;
    }

    /**
     * Verifies an OTP code against a secret key within a time window.
     *
     * @param string $secret The Base32 encoded secret key.
     * @param string $code The OTP code provided by the user.
     * @param int|null $currentTimeSlice The current time slice (defaults to calculated current).
     * @return bool True if the code is valid, false otherwise.
     */
    public function verifyCode(string $secret, string $code, ?int $currentTimeSlice = null): bool
    {
        if ($currentTimeSlice === null) {
            $currentTimeSlice = floor(time() / $this->period); // Use configured period
        }

        // Ensure the code length matches the configured length
        if (strlen($code) != $this->length) { // Use configured length
            return false;
        }

        // Check codes within the allowed time window
        for ($i = -$this->window; $i <= $this->window; ++$i) { // Use configured window
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if ($this->timingSafeEquals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Base32 encodes a binary string.
     * This is a critical security component. Self-implementing requires extreme care.
     *
     * @param string $binaryString The binary data to encode.
     * @return string The Base32 encoded string.
     */
    protected function base32Encode(string $binaryString): string
    {
        // This is a simplified Base32 encoder.
        // For production-grade security, using a well-vetted library (e.g., paragonie/constant_time_encoding)
        // is strongly recommended over self-implementation due to the complexity and security implications
        // of cryptographic operations.
        $encoded = '';
        $bitBuffer = 0;
        $bitCount = 0;

        foreach (str_split($binaryString) as $char) {
            $byte = ord($char);
            $bitBuffer = ($bitBuffer << 8) | $byte;
            $bitCount += 8;

            while ($bitCount >= 5) {
                $shift = $bitCount - 5;
                $index = ($bitBuffer >> $shift) & 0x1F;
                $encoded .= $this->base32Chars[$index];
                $bitCount -= 5;
            }
        }

        if ($bitCount > 0) {
            $bitBuffer <<= (5 - $bitCount); // Pad with zeros
            $index = $bitBuffer & 0x1F;
            $encoded .= $this->base32Chars[$index];
        }

        $padding = '';
        if (strlen($encoded) % 8 !== 0) {
            $paddingLength = 8 - (strlen($encoded) % 8);
            $padding = str_repeat('=', $paddingLength);
        }

        return $encoded . $padding;
    }

    /**
     * Base32 decodes a string.
     * This is a critical security component. Self-implementing requires extreme care.
     *
     * @param string $secret The Base32 encoded string.
     * @return string The decoded binary string.
     * @throws RuntimeException If decoding fails due to invalid characters or padding.
     */
    protected function base32Decode(string $secret): string
    {
        if (empty($secret)) {
            return '';
        }

        // Remove padding characters for processing
        $secret = rtrim($secret, '=');
        $secret = strtoupper($secret); // Base32 is case-insensitive during decoding

        $binaryString = '';
        $bitBuffer = 0;
        $bitCount = 0;

        foreach (str_split($secret) as $char) {
            if (!isset($this->base32CharsFlipped[$char])) {
                throw new RuntimeException('Invalid Base32 character encountered during decoding: ' . $char);
            }
            $value = $this->base32CharsFlipped[$char];

            $bitBuffer = ($bitBuffer << 5) | $value;
            $bitCount += 5;

            while ($bitCount >= 8) {
                $shift = $bitCount - 8;
                $byte = ($bitBuffer >> $shift) & 0xFF;
                $binaryString .= chr($byte);
                $bitCount -= 8;
            }
        }

        return $binaryString;
    }

    /**
     * Performs a timing-safe string comparison.
     * Crucial for preventing timing attacks on OTP verification.
     *
     * @param string $safeString The known safe string.
     * @param string $userString The user-provided string.
     * @return bool True if strings are equal, false otherwise.
     */
    private function timingSafeEquals(string $safeString, string $userString): bool
    {
        // Use PHP's built-in hash_equals for modern PHP versions (PHP 5.6+)
        // It's designed to prevent timing attacks.
        if (function_exists('hash_equals')) {
            return hash_equals($safeString, $userString);
        }

        // Fallback for older PHP versions (less secure, but included for extreme compatibility)
        $safeLen = strlen($safeString);
        $userLen = strlen($userString);

        if ($userLen != $safeLen) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < $userLen; ++$i) {
            $result |= (ord($safeString[$i]) ^ ord($userString[$i]));
        }
        return $result === 0;
    }

    public function getRoute(Request $request)
    {
        $as = $request->route()->action['as'] ?? '';
        if(empty($as)) {
            return null;
        }

        return explode('.', $as);
    }

}