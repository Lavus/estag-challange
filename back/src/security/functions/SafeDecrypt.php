<?php
    declare(strict_types=1);
    function SafeDecrypt(string $encrypted): string {
        require "key/GetKey.php";
        $key = GetKey();
        $regexbase64 = '/^([A-Za-z0-9+\/]{4})*([A-Za-z0-9+\/]{3}=|[A-Za-z0-9+\/]{2}==)?$/';
        if (preg_match($regexbase64,$encrypted)){
            $decoded = base64_decode($encrypted);
            if (strlen($decoded) >= 24){
                $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
                $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
                $plain = sodium_crypto_secretbox_open($ciphertext,$nonce,$key);
                if (!is_string($plain)) {
                    return ('FALSE');
                }
                sodium_memzero($ciphertext);
                sodium_memzero($key);
                return $plain;
            }
        }
        return ('FALSE');
    }
?>