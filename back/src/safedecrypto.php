<?php
    declare(strict_types=1);
    function safeEncrypt(string $message, string $key): string {
        if (mb_strlen($key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RangeException('Key is not the correct size (must be 32 bytes).');
        }
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = base64_encode($nonce.sodium_crypto_secretbox($message,$nonce,$key));
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    function safeDecrypt(string $encrypted, string $key): string {
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

    function codifyhtml(string $text): string {
        $codetext = "";
        $caracters = mb_str_split($text);
        for ($index = 0; $index < count($caracters); $index++) {
            $codetext .= "&#".strval(mb_ord($caracters[$index], "UTF-8")).";";
        }
        return $codetext;
    }
    
    function getkey(): string {
        $myfile = fopen("key.txt", "r") or die("Unable to open file!");
        $keytest = (fread($myfile,filesize("key.txt")));
        $keysafe = '\nH�C>/ C?_ہ�K��k';
        $key = safeDecrypt($keytest, $keysafe);
        return $key;
    }
?>