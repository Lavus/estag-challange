<?php
    declare(strict_types=1);
    function SafeCrypto(string $text, string $type): string {
        if ( $type == "Html") {
            require "functions/CodifyHtml.php";
            return (CodifyHtml($text));
        } else if ( $type == "Encrypt") {
            require "functions/SafeEncrypt.php";
            return (SafeEncrypt($text));
        } else if ( $type == "Decrypt") {
            require "functions/SafeDecrypt.php";
            return (SafeDecrypt($text));
        } else {
            return ("FALSE");
        }
    }
?>