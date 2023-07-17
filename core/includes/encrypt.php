<?php

class Encrypt {

    private static $instance;
    private $method;
    private $key;
    private $separator;

    private function __construct() {
        $this->method = 'AES-128-CBC';
        $this->key = defined( 'EKEY' ) ? EKEY : 'AIO_SECURE';
        $this->separator = ':';
    }

    public static function initiate(): Encrypt {
        if (self::$instance === null) {
            self::$instance = new Encrypt();
        }
        return self::$instance;
    }

    /**
     * Encrypt a text string
     * @param string $string Text to Encrypt
     * @return string
     */
    public function encrypt( string $string ): string {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
        return base64_encode(openssl_encrypt( $string, $this->method, $this->key, 0, $iv) . $this->separator . base64_encode($iv));
    }

    /**
     * Encrypt an array
     * @param array $array Array to Encrypt
     * @return string
     */
    public function encrypt_array( array $array ): string {
        return $this->encrypt( json_encode( $array ) );
    }

    /**
     * Decrypt an array
     * @param string $encryptedString Encrypted Text
     * @return array
     */
    public function decrypt_array( string $encryptedString ): array {
        $decrypt = $this->decrypt( $encryptedString );
        return !empty( $decrypt ) ? json_decode( $decrypt, 1 ) : [];
    }

    /**
     * Decrypt an encrypted string
     * @param string $encryptedString Encrypted text
     * @return string
     */
    public function decrypt( string $encryptedString ): string {
        $parts = explode( $this->separator, base64_decode( $encryptedString ) );
        return is_array($parts) && count($parts) > 1 ? openssl_decrypt($parts[0], $this->method, $this->key, 0, base64_decode($parts[1])) : false;
    }

    public function true(): void {
        echo $this->enc('true');
    }

    public function false(): void  {
        echo $this->enc('false');
    }

    /**
     * Checks if string is encrypted or not
     * @param string $string Text to check
     * @return bool|void
     */
    public function check( string $string = '' ) {
        if( $this->decrypt( $string ) == 'true' ){
            return true;
        } else if ( $this->decrypt( $string ) == 'false' ) {
            return false;
        }
    }

    /**
     * Echo a plain string to decrypted string
     * @param string $string String to be encrypted
     */
    public function enc( string $string ): void {
        echo $this->encrypt( $string );
    }

    /**
     * Echo a string after decryption
     * @param string $encryptedString Encrypted text to be decrypted
     */
    public function dec( string $encryptedString ): void {
        echo $this->decrypt( $encryptedString );
    }

    /**
     * Echo array as encrypted string
     * @param array $data Array to be Encrypted
     */
    public function enc_array( array $data ) {
        $this->enc( json_encode( $data ) );
    }

    public function random( $length = 8 ) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

/* GUIDE
$cry = Encrypt::initiate();
$encrypted_text = $cry->encrypt( "Test String" );
$decrypted_text = $cry->decrypt( $encrypted_text );
*/