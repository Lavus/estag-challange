<?php
    declare(strict_types=1);
    function SafeCrypto(string $text, string $type): string {
        if ( $type == "Html") {
            require_once "functions/CodifyHtml.php";
            return (CodifyHtml($text));
        } else if ( $type == "Encrypt") {
            require_once "functions/SafeEncrypt.php";
            return (SafeEncrypt($text));
        } else if ( $type == "Decrypt") {
            require_once "functions/SafeDecrypt.php";
            return (SafeDecrypt($text));
        } else {
            return ("FALSE");
        }
    }
?>