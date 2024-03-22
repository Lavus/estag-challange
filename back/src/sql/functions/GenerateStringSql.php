<?php
    declare(strict_types=1);
    function GenerateStringSql(string $table, array $camps = [], array $campsAlias = []): string {
        $stringCamps = "";
        foreach($camps as $index => $camp) {
            $stringCamps .= $table.".".$camp." AS ".$campsAlias[$index].",";
        }
        $stringCamps = rtrim($stringCamps, ",");
        return ($stringCamps);
    }
?>