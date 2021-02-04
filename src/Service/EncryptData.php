<?php


namespace App\Service;


use Psr\Log\LoggerInterface;

class EncryptData
{
    private const METHOD = 'aes-256-ctr';
    private const ALGO = 'sha256';
    private $key;

    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->key = $_ENV['OPEN_SSL_KEY'];
        $this->logger = $logger;
    }

    /**
     * @param $plaintext
     * @return string
     */
    public function encrypt($plaintext): string
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher=self::METHOD));
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $this->key , $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->key , $as_binary=true);
        $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );

        return $ciphertext;
    }

    /**
     * @param $text
     * @return string
     */
    public function decrypt($encryptText)
    {
        $c = base64_decode($encryptText);
        $ivlen = openssl_cipher_iv_length($cipher=self::METHOD);
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);

        try {
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $this->key , $options=OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac(self::ALGO, $ciphertext_raw, $this->key , $as_binary=true);
            if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
            {
                return $original_plaintext;
            }

            $this->logger->error('data decrypt failed');
            return $encryptText;
        }catch (\Exception $e) {
            return $encryptText;
        }
    }
}