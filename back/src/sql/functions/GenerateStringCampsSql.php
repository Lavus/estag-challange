<?php
    declare(strict_types=1);
    function GenerateStringCampsSql(array $tables = [], array $camps = [], array $campsAlias = []): string {
        $stringCamps = "";
        foreach($tables as $indexTable => $table) {
            if (count($camps) > $indexTable ) {
                foreach($camps[$indexTable] as $indexCamp => $camp) {
                    if ( count($camp) == 1 ){
                        $stringCamps .= $table.".".$camp[0]." AS ".$campsAlias[$indexTable][$indexCamp].",";
                    } else {
                        $stringCamps .= $camp[1]."(".$table.".".$camp[0].") AS ".$campsAlias[$indexTable][$indexCamp].",";
                    }
                }
            }
        }
        $stringCamps = rtrim($stringCamps, ",");
        return ($stringCamps);
    }
?>