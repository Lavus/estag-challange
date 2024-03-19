<?php
    declare(strict_types=1);
    function CodifyHtml(string $text): string {
        $codetext = "";
        $caracters = mb_str_split($text);
        for ($index = 0; $index < count($caracters); $index++) {
            $codetext .= "&#".strval(mb_ord($caracters[$index], "UTF-8")).";";
        }
        return $codetext;
    }
?>