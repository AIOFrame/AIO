<?php

class Crypto {

    private static $instance;
    private $method;
    private $key;
    private $separator;

    private function __construct() {
        $this->method = 'AES-128-CBC';
        $this->key = defined( 'EKEY' ) ? EKEY : 'AIO_SECURE';
        $this->separator = ':';
    }

    public static function initiate() {
        if (self::$instance === null) {
            self::$instance = new Crypto();
        }
        return self::$instance;
    }

    /**
     * Encrypt a text string
     * @param string $data Text to Encrypt
     * @return string
     */
    public function encrypt( string $data ): string {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->method));
        return base64_encode(openssl_encrypt($data, $this->method, $this->key, 0, $iv) . $this->separator . base64_encode($iv));
    }

    /**
     * Decrypt an encrypted string
     * @param string $dataAndVector Encrypted string
     * @return string
     */
    public function decrypt( string $dataAndVector ): string {
        $parts = explode($this->separator, base64_decode($dataAndVector));
        return is_array($parts) && count($parts) > 1 ? openssl_decrypt($parts[0], $this->method, $this->key, 0, base64_decode($parts[1])) : false;
    }

    public function true(){
        echo $this->enc('true');
    }

    public function false() {
        echo $this->enc('false');
    }

    public function check( $data ) {
        if( $this->decrypt( $data ) == 'true' ){
            return true;
        } else if ( $this->decrypt( $data ) == 'false' ) {
            return false;
        }
    }

    public function enc( $data ){
        echo $this->encrypt( $data );
    }

    public function dec( $data ){
        echo $this->decrypt( $data );
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

// HOW TO USE

// Fetch the class
// $cry = Crypto::initiate();

// TO ENCRYPT
// $my_secret = $cry->encrypt("Test String");

// TO DECRYPT
//$cry->decrypt( $my_secret );
