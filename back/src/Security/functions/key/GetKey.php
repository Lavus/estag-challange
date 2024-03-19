<?php
    declare(strict_types=1);
    function GetKey(): string {
        $myfile = fopen("key.txt", "r") or die("Unable to open file!");
        $keytest = (fread($myfile,filesize("key.txt")));
        $keysafe = '\nH�C>/ C?_ہ�K��k';
        $key = safeDecrypt($keytest, $keysafe);
        return $key;
    }
?>