<?php
    declare(strict_types=1);
    function SafeEncrypt(string $message): string {
        require_once "key/GetKey.php";
        $key = GetKey();
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = base64_encode($nonce.sodium_crypto_secretbox($message,$nonce,$key));
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }
?>