<?php
    declare(strict_types=1);
    function GenerateStringTablesSql(array $innerTables = [], array $innerTablesAlias = []): string {
        $stringTables = "";
        foreach($innerTables as $indexTable => $innerTable) {
            if ( $innerTablesAlias == [] ) {
                $stringTables .= $innerTable.",";
            } else {
                $stringTables .= $innerTable." AS ".$innerTablesAlias[$indexTable].",";
            }
        }
        $stringTables = rtrim($stringTables, ",");
        return ($stringTables);
    }
?>